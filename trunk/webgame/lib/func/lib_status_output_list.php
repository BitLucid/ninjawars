<?php
/*
 * Returns a comma-seperated string of states based on the statuses of the target.
 * @param array $statuses status array
 * @param string $target the target, username if self targetting.
 * @return string
 *
 * @package player
 * @subpackage status
 */

function status_output_list($statuses=null, $target=null) {
	$states = array();
	$result = '';
	$target = isset($target)? $target : (isset($SESSION['username']) ? $SESSION['username'] : null);
	if (!$statuses) {
		$statuses = getStatus($target);
	}
	$health = getHealth($target);
	
	if ($health == 0) { $states[] = "Dead"; }
	else { // *** Other statuses only display if not dead.
		if ($health < 80) { $states[] = "Injured"; }
		else {
			$states[] = "Healthy";
		}
		if ($statuses['Stealth']) { $states[] = "Stealthed"; }
		if ($statuses['Poison']) { $states[] = "Poisoned"; }
		if ($statuses['Frozen']) { $states[] = "Frozen"; }
	}
	$result = implode(", ", $states);
	assert($target != '' && $result != '');
	return $result;
}
	
?>
