<?php
namespace NinjaWars\core\control;

use NinjaWars\core\data\ClanFactory;
use NinjaWars\core\data\Message;
use NinjaWars\core\data\Clan;
use \Player as Player;
use NinjaWars\core\data\DatabaseConnection as DatabaseConnection;

/**
 * Controller for all actions involving clan
 */
class ClanController { //extends Controller
	const CLAN_CREATOR_MIN_LEVEL = 20;
	const ALIVE                  = false;
	const PRIV                   = false;

	/**
	 * View information about a clan
	 *
	 * @param int $clan_id (optional) The id of the clan to view
	 * @return Array The viewspec
	 * @note
	 * If a clan_id is not specified, the clan of the current user will be used
	 */
	public function view() {
		$clanID = (int)in('clan_id', null);
        $player = Player::find(self_char_id());

		if (!$clanID) {
			if ($player) {
				$clan = ClanFactory::clanOfMember($player);
			}
		} else {
			$clan = ClanFactory::find($clanID);
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
				'clans'     => ClanFactory::clansRanked(),
				'error'     => 'The clan you requested does not exist. Pick one from the list below',
				'pageParts' => [
					'list',
				],
			];
		}

		if ($player) {
			$myClan = ClanFactory::clanOfMember($player);

			if ($myClan) {
				if ($clan) {
					if ($this->playerIsLeader($player, $clan)) {
						array_unshift($parts['pageParts'], 'manage');
					} else if ($myClan->id() === $clan->id()) {
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
	 * @param int $person_invited The id of the character to invite
	 * @return Array The viewspec
	 * @throws Exception You cannot use this function if you are not the leader of a clan
	 */
	public function invite() {
		$player = new Player(self_char_id());
		$clan   = ClanFactory::clanOfMember($player);

		if (!$this->playerIsLeader($player, $clan)) {
			throw new \Exception('You must be a clan leader to invite new members');
		}

		$person_to_invite = new Player(in('person_invited', ''));

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
	 * @return Array The viewspec
	 * @throws Exception If you are the only leader of your clan, you cannot leave, you must disband
	 */
	public function leave() {
		$player = new Player(self_char_id());
		$clan   = ClanFactory::clanOfMember($player);

		if ($this->playerIsLeader($player, $clan)) {
			throw new \Exception('You are the only leader of your clan. You must disband your clan if you wish to leave.');
		}

		$clan->leave($player);

		return $this->render([
			'action_message' => 'You have left your clan.',
			'title'          => 'You have left your clan.',
			'clans'          => ClanFactory::clansRanked(),
			'pageParts'      => [
				'reminder-no-clan',
				'list',
			]
		]);
	}

	/**
	 * Creates a new clan with the current user as the leader
	 *
	 * @return Array The viewspec
	 * @note
	 * Player must be high enough level to create a clan
	 *
	 * @see CLAN_CREATOR_MIN_LEVEL
	 */
	public function create() {
		$player = new Player(self_char_id());

		if ($player->level() >= self::CLAN_CREATOR_MIN_LEVEL) {
			$default_clan_name = 'Clan '.$player->name();

			while (!Clan::isUniqueClanName($default_clan_name)) {
				$default_clan_name = $default_clan_name.rand(1,999);
			}

			$clan = Clan::createClan($player->id(), $default_clan_name);

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
				'clans'     => ClanFactory::clansRanked(),
				'pageParts' => [
					'list',
				],
			];
		}

		return $this->render($parts);
	}

	/**
	 * Sends a request to a clan leader for the current user to join a clan
	 *
	 * @return Array The viewspec
	 */
	public function join() {
		$clanID = (int) in('clan_id', null);
		$clan   = ClanFactory::find($clanID);

		$this->sendClanJoinRequest(self_char_id(), $clanID);

		$leader = $clan->getLeaderInfo();

		return $this->render([
			'action_message' => "Your request to join {$clan->getName()} has been sent to $leader[uname]",
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
	 * @return Array The viewspec
	 * @throws \Exception The player disbanding must be the leader of the clan
	 */
	public function disband() {
		$player = new Player(self_char_id());
		$clan   = ClanFactory::clanOfMember($player);
		$sure   = in('sure', '');

		if (!$this->playerIsLeader($player, $clan)) {
			throw new \Exception('You may not disband a clan you are not a leader of.');
		}

		if ($sure === 'yes') {
			$clan->disband();

			$parts = [
				'action_message' => 'Your clan has been disbanded.',
				'title'          => 'Clan disbanded',
				'clans'          => ClanFactory::clansRanked(),
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
	 * @param int $kicked The id of the player to kick
	 * @return Array The viewspec
	 * @throws Exception The player must be the leader of the clan to kick a member
	 */
	public function kick() {
		$kicker = new Player(self_char_id());
		$clan   = ClanFactory::clanOfMember($kicker);
		$kicked = in('kicked', '');
		$kicked_name = get_char_name($kicked);

		if (!$this->playerIsLeader($kicker, $clan)) {
			throw new \Exception('You may not kick members from a clan you are not a leader of.');
		}

		$clan->kickMember($kicked, $kicker);

		return $this->render([
			'action_message' => "You have removed $kicked_name from your clan",
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
	 * @param string $clan-avatar-url A url to an image to use as the clan icon
	 * @param string $clan-description A single paragraph describing the clan
	 * @param string $new_clan_name The desired new name of the clan
	 * @return Array The viewspec
	 * @throws Exception Only leaders can update clan details
	 * @note
	 * All parameters are options
	 */
	public function update() {
		$player = new Player(self_char_id());
		$clan   = ClanFactory::clanOfMember($player);

		if (!$this->playerIsLeader($player, $clan)) {
			throw new \Exception('You may not update a clan you are not a leader of.');
		}

		$new_clan_avatar_url  = in('clan-avatar-url');
		$new_clan_description = in('clan-description');
		$new_clan_name        = trim(in('new_clan_name', ''));
		$error                = null;

		if ($new_clan_name != $clan->getName()) {
			if (Clan::isValidClanName($new_clan_name)) {
				if (Clan::isUniqueClanName($new_clan_name)) {
					// *** Rename the clan if it is valid.
					$new_clan_name = Clan::renameClan($clan->getID(), $new_clan_name);
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
			Clan::saveClanAvatarUrl($new_clan_avatar_url, $clan->getID());
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
			Clan::saveClanDescription($new_clan_description, $clan->getID());
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
	 * @return Array The viewspec
	 * @see update()
	 */
	public function edit() {
		$player = new Player(self_char_id());
		$clan   = ClanFactory::clanOfMember($player);

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
	 * @param string $message The message to send to all clan members
	 * @return Array The view spec
	 */
	public function message() {
		$player = new Player(self_char_id());
		$message = in('message', null, null); // Don't filter messages

		if ($player->id()) {
			$myClan = ClanFactory::clanOfMember($player);

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
				return $this->listClans();
			}
		} else {
			return $this->listClans();
		}
	}

	/**
	 * Shows a ranked list of all clans in the game
	 *
	 * @return Array The viewspec
	 */
	public function listClans() {
		$parts = [
			'title'     => 'Clan List',
			'clans'     => ClanFactory::clansRanked(),
			'pageParts' => ['list'],
		];

		$player = Player::find(self_char_id());

		if ($player) {
			$clan = ClanFactory::clanOfMember($player);

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
	 * @param int $joiner The id of the player object to accept into the clan
	 * @param int $confirmation The nonce of clan invitation created by the request to join
	 * @return Array The viewspec
	 */
	public function review() {
		$ninja = new Player(self_char_id());
		$clan  = ClanFactory::clanOfMember($ninja->id());

		$joiner = new Player(in('joiner'));
		$confirmation = (int) in('confirmation');

		$parts = [
			'title' => 'Accept a New Clan Member',
		];

		if ($clan && $this->playerIsLeader($ninja, $clan)) {
			if ($joiner->id()) {
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
	 * @param int $joiner the id of the player object to accept into the clan
	 * @param int $confirmation A nonce that prevents clan leaders from adding unwilling members
	 * @return Array The viewspec
	 *
	 * @par Preconditions:
	 * Active player must be leader of a clan
	 */
	public function accept() {
		$ninja = new Player(self_char_id());
		$clan  = ClanFactory::clanOfMember($ninja->id());

		$joiner = new Player(in('joiner'));
		$confirmation = (int) in('confirmation');

		$parts = [
			'title' => 'Accept a New Clan Member',
		];

		if ($clan && $this->playerIsLeader($ninja, $clan)) {
			if ($joiner->id()) {
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
	 * Generates a viewspec for rendering pages
	 *
	 * @param Array $p_parts Name-Value pairs of values to send to the view
	 * @return Array A viewspec for rendering
	 */
	private function render($p_parts) {
		if (!isset($p_parts['pageParts'])) {
			$p_parts['pageParts'] = [];
		}

		if (!isset($p_parts['error'])) {
			$p_parts['error'] = null;
		}

		if (!isset($p_parts['action_message'])) {
			$p_parts['action_message'] = null;
		}

		$p_parts['player'] = Player::find(self_char_id());
		$p_parts['myClan'] = ($p_parts['player'] ? ClanFactory::clanOfMember($p_parts['player']) : null);

		$p_parts['clan_creator_min_level'] = self::CLAN_CREATOR_MIN_LEVEL;

		return [
			'template' => 'clan.tpl',
			'title'    => $p_parts['title'],
			'parts'    => $p_parts,
			'options'  => [
				'body_classes' => 'clan',
				'quickstat' => true,
			],
		];
	}

	/**
	 * Tests whether the specified player is a leader of the specified clan
	 *
	 * @param Player $p_objPlayer The player in question
	 * @param Clan $p_objClan The clan to check against
	 * @return boolean
	 */
	private function playerIsLeader(Player $p_objPlayer, Clan $p_objClan) {
		$leaders = $p_objClan->getAllClanLeaders();

		foreach ($leaders AS $leader) {
			if ($leader['player_id'] == $p_objPlayer->id()) {
				return true;
			}
		}

		return false;
	}

    /**
     * ????
     *
     * @todo Simplify this invite system.
     * @param int $user_id
     * @param int $clan_id
     * @return void
     */
    private function sendClanJoinRequest($user_id, $clan_id) {
        DatabaseConnection::getInstance();
        $clan_obj  = new Clan($clan_id);
        $leader    = $clan_obj->getLeaderInfo();
        $leader_id = $leader['player_id'];
        $username  = get_char_name($user_id);

        $confirmStatement = DatabaseConnection::$pdo->prepare('SELECT verification_number FROM players WHERE player_id = :user');
        $confirmStatement->bindValue(':user', $user_id);
        $confirmStatement->execute();
        $confirm = $confirmStatement->fetchColumn();

        // These ampersands get encoded later.
        $url = message_url("clan.php?joiner=$user_id&command=review&confirmation=$confirm", 'Confirm Request');

        $join_request_message = 'CLAN JOIN REQUEST: '.htmlentities($username)." has sent a request to join your clan.
            If you wish to allow this ninja into your clan click the following link:
                $url";

Message::create([
    'send_from' => $user_id,
    'send_to'   => $leader_id,
    'message'   => $join_request_message,
    'type'      => 0,
]);
    }

}
