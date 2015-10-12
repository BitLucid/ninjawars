<?php
require_once(LIB_ROOT.'control/lib_inventory.php');

class DoshinController { //extends controller
	public static $alive = true;
	public static $private = false;

	protected $sessionData = [];

	public function __construct() {
		$this->sessionData = [
			'username' => self_name(),
			'char_id'  => self_char_id(),
		];
	}

	public function index() {
		$this->render(
			[
				'quickstat' => false,
				'location'  => 0,
				'error'     => 0,
				'command'   => 'index',
			]
		);
	}

	public function offerBounty() {
		$target    = in('target');
		$target_id = get_char_id($target); // Will be the enemy to put the bounty on.
		$amount    = intval(in('amount'));
		$amount_in = $amount;
		$error     = 0;
		$quickstat = false;
		$success   = false;

		if (!$target_id) {
			$error = 1;
		} else { // Target existed.
			$target_bounty = getBounty($target_id);

			if ($target_bounty < 5000) {
				if ($amount > 0) {
					if (($target_bounty + $amount) > 5000) {
						$amount = (5000 - $target_bounty);
					}

					if (get_gold($this->sessionData['char_id']) >= $amount) {
						addBounty($target_id, $amount); // Add the bounty to the person being bountied upon.  How the hell did this break?

						subtract_gold($this->sessionData['char_id'], $amount);
						send_event($this->sessionData['char_id'], get_char_id($target), $this->sessionData['username']." has offered $amount gold in reward for your head!");

						$success = true;
						$quickstat = 'player';
					} else {
						$error = 2;
					}
				} else {
					$error = 3;
				}
			} else {
				$error = 4;
			}
		}

		$this->render(
			[
				'error'     => $error,
				'success'   => $success,
				'quickstat' => $quickstat,
				'amount_in' => $amount_in,
				'amount'    => $amount,
				'command'   => 'offer',
				'location'  => 0,
				'target'    => $target,
			]
		);
	}

	public function bribe() {
		$bribe = intval(in('bribe'));
		$error = 0;
		$quickstat = false;

		if ($bribe <= get_gold($this->sessionData['char_id']) && $bribe > 0) {
			subtract_gold($this->sessionData['char_id'], $bribe);
			subtractBounty($this->sessionData['char_id'], ($bribe/2));
			$location = 1;

			$quickstat = 'player';
		} else if ($bribe < 0) {
			// Was a bug, now the doshin beats you up!  Yay!
			if (get_gold($this->sessionData['char_id']) > 1000) { //  *** If they have more than 1000 gold, their bounty will be mostly removed by this event.
				$bountyReduction = (getBounty($this->sessionData['char_id']) * .7);
				subtractBounty($this->sessionData['char_id'], $bountyReduction);
			}

			subtractGold($this->sessionData['username'], floor(getGold($this->sessionData['username']) *.8));  //Takes away 80% of the players gold.

			$location = 2;

			$quickstat = 'player';
		} else {
			$location = 0;
			$error = 5;
		}

		$this->render(
			[
				'error'     => $error,
				'quickstat' => $quickstat,
				'location'  => $location,
				'command'   => 'bribe',
			]
		);
	}

	public function render($p_data) {
		$myBounty = getBounty($this->sessionData['char_id']);

		// Pulling the bounties.
		DatabaseConnection::getInstance();
		$result = DatabaseConnection::$pdo->query("SELECT player_id, uname, bounty, class_name AS class, level, clan_id, clan_name FROM players JOIN class ON class_id = _class_id LEFT JOIN clan_player ON player_id = _player_id LEFT JOIN clan ON clan_id = _clan_id WHERE bounty > 0 AND active = 1 and health > 0 ORDER BY bounty DESC");

		$data = $result->fetchAll();

		$p_data['data'] = $data;
		$p_data['myBounty'] = $myBounty;

		display_page(
			'doshin.tpl'
			, 'Doshin Office'
			, $p_data
			, [
				'quickstat' => $p_data['quickstat'],
			]
		);
	}
}

if ($error = init(DoshinController::$private, DoshinController::$alive)) {
	display_error($error);
} else {
	$doshin = new DoshinController();

	$command = in('command');

	switch ($command) {
		case 'Offer Bounty':
			$doshin->offerBounty();
			break;
		case 'Bribe':
			$doshin->bribe();
			break;
		default:
			$doshin->index();
			break;
	}
}
