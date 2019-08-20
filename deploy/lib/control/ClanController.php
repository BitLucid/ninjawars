<?php
namespace NinjaWars\core\control;

use Pimple\Container;
use NinjaWars\core\Filter;
use NinjaWars\core\control\AbstractController;
use NinjaWars\core\data\Message;
use NinjaWars\core\data\Clan;
use NinjaWars\core\data\Player;
use NinjaWars\core\extensions\SessionFactory;
use NinjaWars\core\extensions\StreamedViewResponse;
use NinjaWars\core\environment\RequestWrapper;
use NinjaWars\core\data\DatabaseConnection;

/**
 * Controller for all actions involving clan
 */
class ClanController extends AbstractController {
	const CLAN_CREATOR_MIN_LEVEL = 20;
	const ALIVE                  = false;
	const PRIV                   = false;

	/**
	 * View information about a clan
	 *
     * @param Container $p_dependencies 
	 * @return Response
	 * @note
	 * If a clan_id is not specified, the clan of the current user will be used
	 */
	public function view(Container $p_dependencies) {
        $clanID = RequestWrapper::getPostOrGet('clan_id', null);
        $player = $p_dependencies['current_player'];

        if ($clanID === null && $player) {
            $clan = Clan::findByMember($player);
        } else {
            $clan = $clanID? Clan::find($clanID) : null;
        }

		if (isset($clan) && $clan instanceof Clan) {
			$parts = [
				'title'     => $clan->getName().' Clan',
				'clan'      => $clan,
				'pageParts' => [
					'info',
					'member-list',
				],
			];
		} else {
			$parts = [
				'title'     => 'Clan Not Found',
				'clans'     => Clan::rankings(),
				'error'     => 'The clan you requested does not exist. Pick one from the list below',
				'pageParts' => [
					'list',
				],
			];
		}

		if ($player) {
			$myClan = Clan::findByMember($player);

			if ($myClan) {
				if ($clan) {
					if ($this->playerIsLeader($player, $clan)) {
						array_unshift($parts['pageParts'], 'manage');
					} elseif ($myClan->id === $clan->id) {
						array_unshift($parts['pageParts'], 'non-leader-panel');
					} else {
						array_unshift($parts['pageParts'], 'reminder-member');
					}
				} else {
					array_unshift($parts['pageParts'], 'reminder-member');
				}
			} else {
				array_unshift($parts['pageParts'], 'join');
				array_unshift($parts['pageParts'], 'reminder-no-clan');
			}
		}

		return $this->render($parts);
	}

	/**
	 * Send an invitation to a character that will ask them to join your clan
	 *
     * @param Container $p_dependencies 
	 * @return Response
	 * @throws Exception You cannot use this function if you are not the leader of a clan
	 */
	public function invite(Container $p_dependencies) {
		$player = $p_dependencies['current_player'];
		$clan   = Clan::findByMember($player);

		if (!$clan || !$this->playerIsLeader($player, $clan)) {
			throw new \RuntimeException('You must be a clan leader to invite new members');
		}

		$person_to_invite = Player::findByName(RequestWrapper::getPostOrGet('person_invited', ''));

		$parts = [
			'clan'      => $clan,
			'title'     => 'Invite players to your clan',
			'pageParts' => [
				'edit',
				'info',
				'member-list',
			],
		];

		if ($person_to_invite) {
			$error = $clan->invite($person_to_invite, $player);

			if ($error === null) {
				$parts['action_message'] = 'Invited '.$person_to_invite->name().' to your clan.';
			} else {
				$parts['error'] = $error;
			}
		} else {
			$parts['error'] = 'Sorry, unable to find a ninja to invite by that name.';
		}

		return $this->render($parts);
	}

	/**
	 * Removes the active player from the clan they are in
	 *
     * @param Container $p_dependencies 
	 * @return Response
	 * @throws Exception If you are the only leader of your clan, you cannot leave, you must disband
	 */
	public function leave(Container $p_dependencies) {
		$player = $p_dependencies['current_player'];
		$clan   = Clan::findByMember($player);

		if ($this->playerIsLeader($player, $clan)) {
			throw new \RuntimeException('You are the only leader of your clan. You must disband your clan if you wish to leave.');
		}

		$clan->leave($player);

		return $this->render([
			'action_message' => 'You have left your clan.',
			'title'          => 'You have left your clan.',
			'clans'          => Clan::rankings(),
			'pageParts'      => [
				'reminder-no-clan',
				'list',
			]
		]);
	}

	/**
	 * Creates a new clan with the current user as the leader
	 *
     * @param Container $p_dependencies 
	 * @return Response
	 * @note
	 * Player must be high enough level to create a clan
	 *
	 * @see CLAN_CREATOR_MIN_LEVEL
	 */
	public function create(Container $p_dependencies) {
		$player = $p_dependencies['current_player'];

		if ($player->level >= self::CLAN_CREATOR_MIN_LEVEL) {
			$default_clan_name = 'Clan '.$player->name();

			while (!Clan::isUniqueClanName($default_clan_name)) {
				$default_clan_name = $default_clan_name.rand(1,999);
			}

			$clan = Clan::create($player, $default_clan_name);

			$parts = [
				'action_message' => 'Your clan was created with the default name: '.$clan->getName().'. Change it below.',
				'title'          => 'Clan '.$clan->getName(),
				'clan'           => $clan,
				'pageParts'      => [
					'edit',
				],
			];
		} else {
			$parts = [
				'error'     => 'You do not have enough renown to create a clan. You must be at least level '.self::CLAN_CREATOR_MIN_LEVEL.'.',
				'title'     => 'You cannot create a clan yet',
				'clans'     => Clan::rankings(),
				'pageParts' => [
					'list',
				],
			];
		}

		return $this->render($parts);
	}

	/**
	 * Sends a request to a clan leader for the current user to join a clan
	 * @param Container $p_dependencies 
	 * @return Response
	 */
	public function join(Container $p_dependencies): StreamedViewResponse {
		$clanID = (int) RequestWrapper::getPostOrGet('clan_id', 0);
		$clan   = Clan::find($clanID);
		
		$leader = $clan->getLeaderInfo();

		$message = 'The leader to this clan is inactive, try another.';
		if (!empty($leader)) {
			$available = $this->sendClanJoinRequest($p_dependencies['session']->get('player_id'), $clanID);
			$message = "Your request to join {$clan->getName()} has been sent to $leader[uname]";
		}

		return $this->render([
			'action_message' => $message,
			'title'          => 'Viewing a clan',
			'clan'           => $clan,
			'pageParts'      => [
				'reminder-no-clan',
				'info',
				'member-list',
			],
		]);
	}

	/**
	 * Deletes a clan and messages all members that it has been disbanded
	 *
	 * @param Container $p_dependencies 
	 * @throws \RuntimeException On invalid leader disband request
	 * @return Response
	 */
	public function disband(Container $p_dependencies): StreamedViewResponse {
		$player = $p_dependencies['current_player'];
		$clan   = Clan::findByMember($player);
		$sure   = RequestWrapper::getPostOrGet('sure', '');

		if (!$this->playerIsLeader($player, $clan)) {
			throw new \RuntimeException('You may not disband a clan you are not a leader of.');
		}

		if ($sure === 'yes') {
			$clan->disband();

			$parts = [
				'action_message' => 'Your clan has been disbanded.',
				'title'          => 'Clan disbanded',
				'clans'          => Clan::rankings(),
				'pageParts'      => [
					'reminder-no-clan',
					'list',
				],
			];
		} else {
			$parts = [
				'title'     => 'Confirm disbanding of your clan',
				'pageParts' => [
					'confirm-disband',
				],
			];
		}

		return $this->render($parts);
	}

	/**
	 * Removes a player from a clan
	 *
     * @param Container $p_dependencies 
	 * @return Response
	 * @throws Exception The player must be the leader of the clan to kick a member
	 */
	public function kick(Container $p_dependencies) {
		$kicker = $p_dependencies['current_player'];
		$clan   = Clan::findByMember($kicker);
		$kicked = Player::find(RequestWrapper::getPostOrGet('kicked', ''));

		if (!$this->playerIsLeader($kicker, $clan)) {
			throw new \RuntimeException('You may not kick members from a clan you are not a leader of.');
		}

		$clan->kickMember($kicked->id(), $kicker);

		return $this->render([
			'action_message' => "You have removed ".$kicked->name()." from your clan",
			'title'          => 'Manage your clan',
			'clan'           => $clan,
			'pageParts'      => [
				'manage',
				'info',
				'member-list',
			]
		]);
	}

	/**
	 * Edits clan metadata
	 *
     * @todo accumulate error messages
     * @param Container $p_dependencies 
	 * @return Response
	 * @throws Exception Only leaders can update clan details
	 * @note
	 * All parameters are options
	 */
	public function update(Container $p_dependencies) {
        $request = RequestWrapper::$request;
		$player  = $p_dependencies['current_player'];
		$clan    = Clan::findByMember($player);

		if (!$this->playerIsLeader($player, $clan)) {
			throw new \Exception('You may not update a clan you are not a leader of.');
		}

		$new_clan_avatar_url  = $request->get('clan-avatar-url');
		$new_clan_description = $request->get('clan-description');
		$new_clan_name        = trim($request->get('new_clan_name', ''));
		$error                = null;

		if ($new_clan_name != $clan->getName()) {
			if (Clan::isValidClanName($new_clan_name)) {
				if (Clan::isUniqueClanName($new_clan_name)) {
					// *** Rename the clan if it is valid.
					$new_clan_name = Clan::renameClan($clan->id, $new_clan_name);
					$clan->setName($new_clan_name);
				} else {
					$error = 'That clan name is already in use!';
				}
			} elseif ($new_clan_name) {
				$error = 'Sorry, too many special symbols in your clan name.';
			}
		}

		// Saving incoming changes to clan leader edits.
		if (Clan::clanAvatarIsValid($new_clan_avatar_url)) {
			Clan::saveClanAvatarUrl($new_clan_avatar_url, $clan->id);
			$clan->setAvatarUrl($new_clan_avatar_url);
		} else {
			$error = 'That avatar url is not valid.';
		}

		// Truncate at 500 chars if necessary.
		$truncated_clan_desc = substr((string)$new_clan_description, 0, 500);
		if ($truncated_clan_desc != (string) $new_clan_description) {
			$new_clan_description = $truncated_clan_desc;
		}

		if ($new_clan_description) {
			Clan::saveClanDescription($new_clan_description, $clan->id);
			$clan->setDescription($new_clan_description);
		}

		return $this->render([
			'action_message' => 'Your clan has been updated.',
			'title'          => 'Edit your clan',
			'clan'           => $clan,
			'error'          => $error,
			'pageParts'      => [
				'edit',
				'info',
				'member-list',
			],
		]);
	}

	/**
	 * Shows a form for editing clan metadata
	 *
     * @param Container $p_dependencies 
	 * @return Response
	 * @see update()
	 */
	public function edit(Container $p_dependencies) {
		$player = $p_dependencies['current_player'];
		$clan   = Clan::findByMember($player);

		return $this->render([
			'clan'      => $clan,
			'title'     => 'Edit your clan',
			'pageParts' => [
				'edit',
				'info',
			],
		]);
	}

	/**
	 * Sends a message to all members of the clan of the current player
	 *
     * @param Container $p_dependencies 
	 * @return Response
	 */
	public function message(Container $p_dependencies) {
		$player = $p_dependencies['current_player'];
		$message = RequestWrapper::getPostOrGet('message', null);

		if ($player) {
			$myClan = Clan::findByMember($player);

			if ($myClan) {
				$target_id_list = $myClan->getMemberIds();

				Message::sendToGroup($player, $target_id_list, $message, 1);

				$parts = [
					'clan'           => $myClan,
					'title'          => 'Your clan',
					'action_message' => 'Message sent to your clan.',
					'pageParts'      => [
						'info',
						'member-list',
					],
				];

				if ($this->playerIsLeader($player, $myClan)) {
					array_unshift($parts['pageParts'], 'manage');
				} else {
					array_unshift($parts['pageParts'], 'non-leader-panel');
				}

				return $this->render($parts);
			} else {
				return $this->listClans($p_dependencies);
			}
		} else {
			return $this->listClans($p_dependencies);
		}
	}

	/**
	 * Shows a ranked list of all clans in the game
	 *
     * @param Container $p_dependencies 
	 * @return Response
	 */
	public function listClans(Container $p_dependencies) {
		$parts = [
			'title'     => 'Clan List',
			'clans'     => Clan::rankings(),
			'pageParts' => ['list'],
		];

		$player = $p_dependencies['current_player'];

		if ($player) {
			$clan = Clan::findByMember($player);

			if ($clan) {
				array_unshift($parts['pageParts'], 'reminder-member');
			} else {
				array_unshift($parts['pageParts'], 'reminder-no-clan');
			}
		}

		return $this->render($parts);
	}

	/**
	 * Review a request to join a clan
	 *
     * @param Container $p_dependencies 
	 * @return Response
	 */
	public function review(Container $p_dependencies) {
        $request = RequestWrapper::$request;
		$ninja   = $p_dependencies['current_player'];
		$clan    = Clan::findByMember($ninja);

		$joiner = Player::find($request->get('joiner'));
		$confirmation = (int) $request->get('confirmation');

		$parts = [
			'title' => 'Accept a New Clan Member',
		];

		if ($clan && $this->playerIsLeader($ninja, $clan)) {
			if ($joiner) {
				$parts['pageParts'] = [
					'reminder-join-request',
					'form-confirm-join',
				];

				$parts['joiner'] = $joiner;
				$parts['confirmation'] = $confirmation;
			} else {
				$parts['error'] = 'No such ninja to bring into the clan.';
			}
		} else {
			$parts['error'] = 'You are not the leader of your clan.';
		}

		return $this->render($parts);
	}

	/**
	 * Accept a player as a new member of a clan
	 *
     * @param Container $p_dependencies 
	 * @return Response
	 *
	 * @par Preconditions:
	 * Active player must be leader of a clan
	 */
	public function accept(Container $p_dependencies) {
        $request = RequestWrapper::$request;
		$ninja   = $p_dependencies['current_player'];
		$clan    = Clan::findByMember($ninja);

		$joiner = Player::find($request->get('joiner'));
		$confirmation = (int) $request->get('confirmation');

		$parts = [
			'title' => 'Accept a New Clan Member',
		];

		if ($clan && $this->playerIsLeader($ninja, $clan)) {
			if ($joiner) {
				$parts['pageParts'] = [
					'reminder-join-request',
				];

				$parts['joiner'] = $joiner;

				// Allow joining as long as the verification number is correct.
				if ($confirmation === $joiner->getVerificationNumber()) {
					$result = $clan->addMember($joiner, $ninja);

					if ($result === true) {
						$parts['pageParts'][] = 'result-join';
					} else {
						$parts['error'] = (is_string($result) ? $result : 'Unable to add member, please try again later.');
					}
				} else {
					$parts['error'] = 'That request was old or invalid, please try inviting that ninja again.';
				}
			} else {
				$parts['error'] = 'No such ninja to bring into the clan.';
			}
		} else {
			$parts['error'] = 'You are not the leader of your clan.';
		}

		return $this->render($parts);
	}

	/**
	 * Generates a Response for rendering pages
	 *
     * @todo inject dependencies instead of pulling from the model
	 * @param Array $p_parts Name-Value pairs of values to send to the view
	 * @return Response
	 */
	private function render(array $p_parts): StreamedViewResponse {
		if (!isset($p_parts['pageParts'])) {
			$p_parts['pageParts'] = [];
		}

		if (!isset($p_parts['error'])) {
			$p_parts['error'] = null;
		}

		if (!isset($p_parts['action_message'])) {
			$p_parts['action_message'] = null;
		}

		$p_parts['player'] = Player::find(SessionFactory::getSession()->get('player_id'));
		$p_parts['myClan'] = ($p_parts['player'] ? Clan::findByMember($p_parts['player']) : null);

		$p_parts['clan_creator_min_level'] = self::CLAN_CREATOR_MIN_LEVEL;

        $options  = [
            'body_classes' => 'clan',
            'quickstat'    => true,
        ];

        return new StreamedViewResponse($p_parts['title'], 'clan.tpl', $p_parts, $options);
	}

	/**
	 * Tests whether the specified player is a leader of the specified clan
	 *
	 * @param Player $p_objPlayer The player in question
	 * @param Clan   $p_objClan   The clan to check against
	 * @return boolean
	 */
	private function playerIsLeader(Player $p_objPlayer, Clan $p_objClan): bool {
		$leaders = $p_objClan->getAllClanLeaders();

		foreach ($leaders AS $leader) {
			if ($leader['player_id'] == $p_objPlayer->id()) {
				return true;
			}
		}

		return false;
	}

    /**
     * Send a message that links a player to a clan join request message
     *
     * @todo Simplify this invite system.
     * @param int $user_id 
     * @param int $clan_id 
	 * @return Array|bool
     */
    private function sendClanJoinRequest(int $user_id, int $clan_id) {
        DatabaseConnection::getInstance();
        $clan_obj  = new Clan($clan_id);
        $leader    = $clan_obj->getLeaderInfo();
        $leader_id = $leader['player_id'];
        $user      = Player::find($user_id);
		$username  = $user->name();
		
		if(!$leader_id){
			return [
				'error'=>true,
				'message'=>'No leader to this clan is available, sorry.'
			];
		}

        $confirmStatement = DatabaseConnection::$pdo->prepare('SELECT verification_number FROM players WHERE player_id = :user');
        $confirmStatement->bindValue(':user', $user_id);
        $confirmStatement->execute();
		$confirm = $confirmStatement->fetchColumn();
		


        // These ampersands get encoded later.
        $url = "[href:clan/review/?joiner=$user_id&confirmation=$confirm|Confirm Request]";

        $join_request_message = 'CLAN JOIN REQUEST: '.htmlentities($username)." has sent a request to join your clan.
            If you wish to allow this ninja into your clan click the following link:
				$url";

        Message::create([
            'send_from' => $user_id,
            'send_to'   => $leader_id,
            'message'   => $join_request_message,
            'type'      => 0,
		]);
		return true;
    }
}
