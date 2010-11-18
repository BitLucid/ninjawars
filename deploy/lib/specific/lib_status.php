<?php
/*
 * @package player
 * @subpackage status
 */


/*
 * Returns a comma-seperated string of states based on the statuses of the target.
 * @param array $statuses status array
 * @param string $target the target, username if self targetting.
 * @return string
 *
 */
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
		if ($target->hasStatus(WEAKENED)) { $states[] = 'Weakened'; }
		if ($target->hasStatus(FROZEN)) { $states[] = 'Frozen'; }
		if ($target->hasStatus(STR_UP1)) { $states[] = 'Buff'; }
		if ($target->hasStatus(STR_UP2)) { $states[] = 'Strength+'; }
		
		// If any of the shield skills are up, show a single status state for any.
		if($target->hasStatus(FIRE_RESISTING) || $target->hasStatus(INSULATED) || $target->hasStatus(GROUNDED)
		    || $target->hasStatus(BLESSED) || $target->hasStatus(IMMUNIZED)
		    || $target->hasStatus(ACID_RESISTING)){
		    $states[] = 'Shielded';
		}	
		
	}

	return $states;
}

function valid_status($dirty){
	if((int)$dirty == $dirty){
		return (int) $dirty;
	} elseif (is_string($dirty)){
		return string_status($dirty);
	} else {
		return null;
	}
}

// Return the value of a defined status constant (or any constant, technically) if it exists.
function string_status($status){
	$status = is_string($status)? strtoupper($status) : null;
	if(defined($status)){
		return constant($status);
	} else {
		return false;
	}
}
?>
