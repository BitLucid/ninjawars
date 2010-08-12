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
 
 
define('STEALTH',     1);
define('POISON',      1<<1);
define('FROZEN',      1<<2);
define('CLASS_STATE', 1<<3);
define('SKILL_1',     1<<4);
define('SKILL_2',     1<<5);
define('INVITED',     1<<6);
define('STR_UP1',     1<<7);
define('STR_UP2',     1<<8);
define('HEALING',     1<<9);

function get_status_list($target=null) {
	$states = array();
	$result = '';
	$target = (isset($target) && (int)$target == $target ? $target : get_char_id());

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
        // The visibly viewable statuses.
		if ($target->hasStatus(STEALTH)) { $states[] = 'Stealthed'; }
		if ($target->hasStatus(POISON)) { $states[] = 'Poisoned'; }
		if ($target->hasStatus(FROZEN)) { $states[] = 'Frozen'; }
		if ($target->hasStatus(STR_UP1)) { $states[] = 'Buff'; }
		if ($target->hasStatus(STR_UP2)) { $states[] = 'Strength+'; }
	}

	return $states;
}
?>
