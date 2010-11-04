<?php
$alive      = false;
$private    = false;

if ($error = init($private, $alive)) {
	display_error($error);
} else {

include(LIB_ROOT.'specific/lib_inventory.php');
$quickstat   = false;
$location    = 'Doshin Office';

$description = array();
$description[] = 'You walk up to the Doshin Office to find the door locked. The Doshin are busy protecting the borders of the village from thieves.';
$description[] = 'Nailed to the door is an official roster of wanted criminals and the bounties offered for their heads.';
$description[] = 'A few men that do seem to be associated with the doshin doze near the entrance. Every so often someone approaches and slips them something that clinks and jingles.';

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
					send_message($char_id, get_char_id($target), "$username has offered $amount gold in reward for your head!");
					
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

		$location    = 'Behind the Doshin Office';
		$description = array();
		$description[] = "\"We'll see what we can do,\" one of the Doshin tells you as you hand off your gold. He then directs you out through a back alley.";
		$description[] = 'You find yourself in a dark alley. A rat scurries by. To your left lies the main street of the village.';

		$quickstat = 'player';
	} else if ($bribe < 0) { // A negative bribe was put in, which on the 21st of March, 2007, was a road to instant wealth, as a bribe of -456345 would increase both your bounty and your gold by 456345, so this will flag players as bugabusers until it becomes a standard-use thing.
		if (get_gold($char_id) > 1000) { //  *** If they have more than 1000 gold, their bounty will be mostly removed by this event.
			$bountyGoesToNearlyZero = (getBounty($username) * .7);
			subtractBounty($username, $bountyGoesToNearlyZero);
		}

		subtractGold($username, floor(getGold($username) *.8));  //Takes away 80% of the players gold.

		$location    = 'The Rat-infested Alley behind the Doshin Office';
		$description = array();
		$description[] = "\"Trying to steal from the Doshin, eh!\" one of the men growls.";
		$description[] = 'Where before there were only relaxing men idly ignoring their duties there are now unsheathed katanas and glaring eyes.';
		$description[] = 'A group of the Doshin advance on you before you can escape and proceed to rough you up with fists and the hilts of their katana.  Finally, they take most of your gold and toss you into the alley behind the building.';
		$description[] = 'Bruised and battered, you find yourself in a dark alley. A rat scurries by. To your left lies the main street of the village.';
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
	, get_certain_vars(get_defined_vars(), array('data', 'description'))
	, array(
		'quickstat' => $quickstat
	)
);
}
?>
