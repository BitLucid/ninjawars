<?php
/*
 *** IMPORTANT MAINTENANCE NOTES ***
 * To disable class change code: set $classChangeAllowed to boolean false
 * To change order of class change cycling: Update $class_array, key = starting class, value = next class in cycle
 */
$private    = false;
$alive      = false;

if ($error = init($private, $alive)) {
	display_error($error);
} else {
$msg            = '';
$dimMakCost     = 40;
$dimMakLevelReq = 10;

$classChangeCost     = 20; // *** Cost of class change in Kills
$classChangeLevelReq = 6;

$class_array = array(
// *** START  => NEXT ***
	'Black'   => 'Red'
	, 'Red'   => 'White'
	, 'White' => 'Gray'
	, 'Gray'  => 'Blue'
	, 'Blue'  => 'Black'
);

$dimmak_sequence     = in('dimmak_sequence', '');
$classChangeSequence = in('classChangeSequence');

if (is_logged_in()) {
	$player    = new Player(get_char_id());
	$userLevel = $player->vo->level;
	$userKills = $player->vo->kills;
	$userClass = $player->vo->class;

	$classChangeAllowed = ($userLevel >= $classChangeLevelReq && $userKills >= $classChangeCost);
	$dimMakAllowed      = ($userLevel >= $dimMakLevelReq && $userKills >= $dimMakCost);

	if ($dimMakAllowed) {	// *** Start of Dim Mak Code, 20 kills. ***
		if ($dimmak_sequence == 2) {
			$userKills = subtractKills($username, $dimMakCost);
			additem($username, 'Dim Mak', 1);
		}
	}	// *** End of Dim Mak Code. ***

	if ($classChangeAllowed && isset($class_array[$userClass]) && $class_array[$userClass]) {
		if ($classChangeSequence == 2) {
			$userKills = subtractKills($username, $classChangeCost);
			setClass($username, $class_array[$userClass]);
		}
	}

	$MAX_LEVEL = 250;

	$nextLevel  = $userLevel + 1;
	$in_upgrade = in('upgrade');
	$required_kills = $userLevel * 5;
	$upgrade_requested = ($in_upgrade && $in_upgrade == 1);

	if ($upgrade_requested) {  // *** If they requested an upgrade ***
		if ($nextLevel < $MAX_LEVEL && $userKills >= $required_kills) {
			$userKills = subtractKills($username, ($userLevel * 5));
			$userLevel = addLevel($username, 1);
			addStrength($username, 5);
			addTurns($username, 50);
			addHealth($username, 100);
		}
	}

}

display_page(
	'dojo.tpl'
	, 'Dojo'
	, array('classChangeAllowed'=>$classChangeAllowed, 'dimMakAllowed'=>$dimMakAllowed, 'dimMakCost'=>$dimMakCost, 'dimmak_sequence'=>$dimmak_sequence, 'classChangeCost'=>$classChangeCost, 'classChangeSequence'=>$classChangeSequence, 'destination_class'=>$class_array[$userClass], 'msg'=>$msg, 'userLevel'=>$userLevel, 'userKills'=>$userKills, 'nextLevel'=>$nextLevel, 'max_level'=>$MAX_LEVEL, 'required_kills'=>$required_kills, 'upgrade_requested'=>$upgrade_requested)
	, array('quickstat'=>'player')
);
}
?>
