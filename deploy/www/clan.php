<?php
require_once(LIB_ROOT.'control/lib_clan.php');
require_once(CORE.'data/ClanFactory.php');

if ($error = init(ClanController::PRIV, ClanController::ALIVE)) {
	display_error($error);
	die();
}

$command = (string)in('command');

$clanController = new ClanController();

switch ($command) {
	case 'view':
		$response = $clanController->view();
		break;
	case 'message':
		$response = $clanController->message();
		break;
	case 'update':
		$response = $clanController->update();
		break;
	case 'edit':
		$response = $clanController->edit();
		break;
	case 'new':
		$response = $clanController->create();
		break;
	case 'leave':
		$response = $clanController->leave();
		break;
	case 'kick':
		$response = $clanController->kick();
		break;
	case 'join':
		$response = $clanController->join();
		break;
	case 'invite':
		$response = $clanController->invite();
		break;
	case 'disband':
		$response = $clanController->disband();
		break;
	case 'list':
		$response = $clanController->listClans();
		break;
	default:
		$response = $clanController->index();
		break;
}

display_page(
	$response['template'],
	$response['title'],
	$response['parts'],
	$response['options']
);

class ClanController { //extends Controller
	const CLAN_CREATOR_MIN_LEVEL = 20;
	const ALIVE                  = false;
	const PRIV                   = false;

	public function index() {
		$player = new Player(self_char_id());
		$myClan = ClanFactory::clanOfMember($player);

		if ($player &&$myClan) {
			return $this->view();
		} else {
			return $this->listClans();
		}
	}

	public function view() {
		$clanID = (int)in('clan_id', null);

		if (!$clanID) {
			$player = new Player(self_char_id());

			if ($player) {
				$clan = ClanFactory::clanOfMember($player);
			}
		} else {
			$clan = ClanFactory::find($clanID);
		}

		if ($clan) {
			$parts = [
				'title'     => $clan->getName(),
				'clan'      => $clan,
				'pageParts' => [
					'info',
					'member-list',
				],
			];

			$player = new Player(self_char_id());

			if ($player) {
				$myClan = ClanFactory::clanOfMember($player);

				if ($myClan) {
					if ($myClan->id() != $clan->id()) {
						array_unshift($parts['pageParts'], 'reminder-member');
					} else if ($this->playerIsLeader($player, $clan)) {
						array_unshift($parts['pageParts'], 'manage');
					} else {
						array_unshift($parts['pageParts'], 'reminder-member');
					}
				} else {
					array_unshift($parts['pageParts'], 'reminder-no-clan');
				}
			}
		} else {
			$parts = [
				'title'     => 'Clan Not Found',
				'clans'     => clans_ranked(),
				'error'     => 'The clan you requested does not exist. Pick one from the list below',
				'pageParts' => [
					'list',
				],
			];
		}

		return $this->render($parts);
	}

	public function invite() {
		$player = new Player(self_char_id());
		$clan   = ClanFactory::clanOfMember($player);

		if (!$this->playerIsLeader($player, $clan)) {
			throw new Exception('You must be a clan leader to invite new members');
		}

		$person_invited = in('person_invited', ''); // A search string
		$person_to_invite = new Player($person_invited);

		$parts = [
			'clan'      => $clan,
			'pageParts' => [
				'manage',
				'info',
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

	public function leave() {
		$player = new Player(self_char_id());
		$clan   = ClanFactory::clanOfMember($player);

		if ($this->playerIsLeader($player, $clan)) {
			throw new Exception('You are the only leader of your clan. You must be disband your clan if you wish to leave.');
		}

		$clan->leave($player);

		return $this->render([
			'action_message' => 'You have left your clan.',
			'clans'          => clans_ranked(),
			'pageParts'      => [
				'list',
			]
		]);
	}

	public function create() {
		$player = new Player(self_char_id());

		if ($player->level() >= ClanController::CLAN_CREATOR_MIN_LEVEL) {
			$default_clan_name = 'Clan '.$player->name();

			while (!is_unique_clan_name($default_clan_name)) {
				$default_clan_name = $default_clan_name.rand(1,999);
			}

			$clan = createClan($player->id(), $default_clan_name);

			$parts = [
				'action_message' => 'You have created a new clan!',
				'clan'           => $clan,
				'pageParts'      => [
					'manage',
					'info',
				],
			];
		} else {
			$parts = [
				'error'     => 'You do not have enough renown to create a clan. You must be at least level '.ClanController::CLAN_CREATOR_MIN_LEVEL.'.',
				'clans'     => clans_ranked(),
				'pageParts' => [
					'list',
				],
			];
		}

		return $this->render($parts);
	}

	public function join() {
		$clan_id_viewed = (int) in('clan_id', null);
		send_clan_join_request(self_char_id(), $clan_id_viewed);

		return $this->render([
			'action_message' => 'Request to join sent.',
			'clan'           => ClanFactory::find($clan_id_viewed),
			'pageParts'      => [
				'info',
			],
		]);
	}

	public function disband() {
		$player = new Player(self_char_id());
		$clan   = ClanFactory::clanOfMember($player);
		$sure   = in('sure', '');

		if (!$this->playerIsLeader($player, $clan)) {
			throw new Exception('You may not disband a clan you are not a leader of.');
		}

		if ($sure === 'yes') {
			$clan->disband();

			$parts = [
				'action_message' => 'Your clan has been disbanded.',
				'clans'          => clans_ranked(),
				'pageParts'      => [
					'list',
				],
			];
		} else {
			$parts = [
				'pageParts' => [
					'confirm-disband',
				],
			];
		}

		return $this->render($parts);
	}

	public function kick() {
		$kicker = new Player(self_char_id());
		$clan   = ClanFactory::clanOfMember($kicker);
		$kicked = in('kicked', '');
		$kicked_name = get_char_name($kicked);

		if (!$this->playerIsLeader($kicker, $clan)) {
			throw new Exception('You may not kick members from a clan you are not a leader of.');
		}

		$clan->kickMember($kicked, $kicker);

		return $this->render([
			'action_message' => "You have removed $kicked_name from your clan",
			'clan'           => $clan,
			'pageParts'      => [
				'info'
			]
		]);
	}

	public function update() {
		$player = new Player(self_char_id());
		$clan   = ClanFactory::clanOfMember($player);

		if (!$this->playerIsLeader($player, $clan)) {
			throw new Exception('You may not update a clan you are not a leader of.');
		}

		$new_clan_avatar_url  = in('clan-avatar-url');
		$new_clan_description = in('clan-description');
		$new_clan_name        = trim(in('new_clan_name', ''));
		$error                = null;

		if ($new_clan_name != $clan->getName()) {
			if (is_valid_clan_name($new_clan_name)) {
				if (is_unique_clan_name($new_clan_name)) {
					// *** Rename the clan if it is valid.
					$new_clan_name = rename_clan($clan->getID(), $new_clan_name);
					$clan->setName($new_clan_name);
				} else {
					$error = 'That clan name is already in use!';
				}
			} elseif ($new_clan_name) {
				$error = 'Sorry, too many special symbols in your clan name.';
			}
		}

		// Saving incoming changes to clan leader edits.
		if (clan_avatar_is_valid($new_clan_avatar_url)) {
			save_clan_avatar_url($new_clan_avatar_url, $clan->getID());
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
			save_clan_description($new_clan_description, $clan->getID());
			$clan->setDescription($new_clan_description);
		}

		///TODO accumulate error messages
		return $this->render([
			'action_message' => 'Your clan has been updated.',
			'title'          => 'Edit your clan',
			'clan'           => $clan,
			'error'          => $error,
			'pageParts'      => [
				'manage',
				'info',
				'member-list',
			],
		]);
	}

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

	public function message() {
		$player = new Player(self_char_id());

		if ($player) {
			$myClan = ClanFactory::clanOfMember($player);

			if ($myClan) {
				$message = in('message', null, null); // Don't filter messages

				message_to_clan($message);

				$myClan = ClanFactory::clanOfMember($player);

				$parts = [
					'clan'           => $clan,
					'title'          => 'Your clan',
					'action_message' => 'Message sent to your clan.',
					'pageParts'      => [
						'info',
					],
				];

				if ($this->playerIsLeader($player, $clan)) {
					array_unshift($parts['pageParts'], 'manage');
				} else {
					array_unshift($parts['pageParts'], 'reminder-member');
				}

				return $this->render($parts);
			} else {
				return $this->listClans();
			}
		} else {
			return $this->listClans();
		}
	}

	public function listClans() {
		$parts = [
			'title'     => 'Clan List',
			'clans'     => clans_ranked(),
			'pageParts' => ['list'],
		];

		$player = new Player(self_char_id());

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

		$p_parts['player'] = new Player(self_char_id());
		$p_parts['myClan'] = ($p_parts['player'] ? ClanFactory::clanOfMember($p_parts['player']) : null);

		$p_parts['clan_creator_min_level'] = ClanController::CLAN_CREATOR_MIN_LEVEL;

		return [
			'template' => 'clan.tpl',
			'title'    => $p_parts['title'],
			'parts'    => $p_parts,
			'options'  => [
				'quickstat' => false,
			],
		];
	}

	private function playerIsLeader($p_objPlayer, $p_objClan) {
		$records = get_clan_leaders($p_objClan->id(), true);

		foreach ($records AS $record) {
			if ($record['player_id'] == $p_objPlayer->id()) {
				return true;
			}
		}

		return false;
	}

}
