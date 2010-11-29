<?php
$alive      = false;
$private    = false;

if ($error = init($private, $alive)) {
	display_error($error);
} else {

require_once(LIB_ROOT.'control/lib_inventory.php');
$quickstat   = false;
$location    = 0;

$target   = in('target');
$command  = in('command');
$username = get_username();
$char_id  = get_char_id();
$amount   = intval(in('amount'));
$bribe    = intval(in('bribe'));
$bounty   = intval(in('bounty'));
$ninja    = in('ninja'); // Ninja to put bounty on.

$amount_in = $amount;

if ($bounty && $ninja && get_user_id($ninja)) {
	$command = 'Offer Bounty';
}

$error = 0;
$success = false;

if ($command == 'Offer Bounty') {
	if (!get_user_id($target)) {
		$error = 1;
	} else {
		$target_bounty = getBounty($target);

		if ($target_bounty < 5000) {
			if ($amount > 0) {
				if (($target_bounty + $amount) > 5000) {
					$amount = (5000 - $target_bounty);
				}

				if (get_gold($char_id) >= $amount) {
					addBounty($target, $amount);

					subtract_gold($char_id, $amount);
					send_event($char_id, get_char_id($target), "$username has offered $amount gold in reward for your head!");
					
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
} else if ($command == 'Bribe') {
	if ($bribe <= get_gold($char_id) && $bribe > 0) {
		subtract_gold($char_id, $bribe);
		subtractBounty($username, ($bribe/2));
		$location = 1;

		$quickstat = 'player';
	} else if ($bribe < 0) { // A negative bribe was put in, which on the 21st of March, 2007, was a road to instant wealth, as a bribe of -456345 would increase both your bounty and your gold by 456345, so this will flag players as bugabusers until it becomes a standard-use thing.
		if (get_gold($char_id) > 1000) { //  *** If they have more than 1000 gold, their bounty will be mostly removed by this event.
			$bountyGoesToNearlyZero = (getBounty($username) * .7);
			subtractBounty($username, $bountyGoesToNearlyZero);
		}

		subtractGold($username, floor(getGold($username) *.8));  //Takes away 80% of the players gold.

		$location = 2;

		$quickstat = 'player';
	} else {
		$error = 5;
	}
}

$myBounty = getBounty($username);

DatabaseConnection::getInstance();
$result = DatabaseConnection::$pdo->query("SELECT player_id, uname, bounty, class_name AS class, level, clan_id, clan_name FROM players JOIN class ON class_id = _class_id LEFT JOIN clan_player ON player_id = _player_id LEFT JOIN clan ON clan_id = _clan_id WHERE bounty > 0 AND active = 1 and health > 0 ORDER BY bounty DESC");

$data = $result->fetchAll();

display_page(
	'doshin.tpl'
	, 'Doshin Office'
	, get_certain_vars(get_defined_vars(), array('data'))
	, array(
		'quickstat' => $quickstat
	)
);
}
?>
