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

function get_status_list($target=null) {
	$states = array();
	$result = '';
	$target = (isset($target) ? $target : get_username());

	// Default to showing own status.
	$target = new Player($target);

	if ($target->vo->health < 1) {
		$states[] = 'Dead';
	} else { // *** Other statuses only display if not dead.
		if ($target->vo->health < 80) {
			$states[] = 'Injured';
		} else {
			$states[] = 'Healthy';
		}

		if ($target->hasStatus(STEALTH)) { $states[] = 'Stealthed'; }
		if ($target->hasStatus(POISON)) { $states[] = 'Poisoned'; }
		if ($target->hasStatus(FROZEN)) { $states[] = 'Frozen'; }
	}

	return $states;
}

function render_status_list($target=null) {
	$states = get_status_list($target);
	$result = implode(', ', $states);

	return $result;
}

function render_status_section($target=null) {
	$res = '';
	$statuses = get_status_list($target);

	if (!empty($statuses)) {
		$res .= '<span class="player-status ninja-notice '.implode(' ', $statuses).'">'.implode(', ', $statuses).'</span>';
	}

	return $res;
}
?>
