<?php
$private   = false;
$alive     = false;

if ($error = init($private, $alive)) {
	display_error($error);
} else {

include(OBJ_ROOT."Skill.php");
$skillsListObj = new Skill();

$healed             = in('healed');
$poisoned           = in('poisoned');
$restore            = in('restore');
$max_heal           = in('max_heal');
$heal_and_resurrect = in('heal_and_resurrect');
$player             = new Player($username);
$startingHealth     = $player->vo->health;
$userLevel          = $player->vo->level;
$startingGold       = $player->vo->gold;
$startingKills      = $player->vo->kills;
$startingTurns      = $player->vo->turns;
$level              = $player->vo->level;
$heal_points        = (in('heal_points') ? intval(in('heal_points')) : null);  // The pointwise healing method.
$freeResLevelLimit  = 6;
$freeResKillLimit   = 25;
$lostTurns          = 10; // *** Default turns lost when the player has no kills.
$has_chi            = $skillsListObj->hasSkill('Chi'); // Extra healing benefits from chi, by level.
$error              = null;
$max_health         = determine_max_health($level);

// *** A boolean for whether or not resurrection will be free.
$freeResurrection   = ($userLevel < $freeResLevelLimit && $startingKills < $freeResKillLimit);

if ($heal_and_resurrect) {
	// Set resurrect if needed.
	if ($startingHealth < 1) {
		$restore = 1;
	}

	// Set heal always.
	$max_heal = 1;
}

if ($restore == 1) {	//  *** RESURRECTION SECTION ***
    $resurrect_requested = true;
    $turn_taking_resurrect = false;
    $kill_taking_resurrect = true;

	if ($startingHealth > 0) {
		$error = 'You are not dead.';
	} else if ($startingKills > 1 || $freeResurrection) {	// If you're dead, and a newbie, or dead and have kills.
		//  *** FREE RESURRECTION DETERMINATION ***

		if (!($freeResurrection)) { // 1 kill point cost for resurrection above level 5 or 25 kills.
			subtractKills($username, 1);
			$kill_taking_resurrect = false;
		}

		$player->death();

		$returning_health = ($has_chi ? 150 : 100);
		setHealth($username, $returning_health);
		$final_turns = (string) getTurns($username);
		$final_kills = getKills($username);
		
		// *** FREE STEALTHING FOR BLACK CLASS UPON NON-FREE RESURRECTION
		if ($players_class == "Black" && (!$freeResurrection)) {
			addStatus($username, STEALTH);
		}
	} elseif ($startingTurns > 0) { // Dead and no killpoints left, and not a newbie.
		if ($startingTurns < $lostTurns && $startingTurns > 0) { // *** From 9 to 1 turns.
			$lostTurns = $startingTurns;
		}

		$player->death();

		subtractTurns($username, $lostTurns); // *** Takes away necessary turns.
		setHealth($username, 100);
		$final_turns = (string) getTurns($username);
		
		$turn_taking_resurrect = true; // Template display variable.
	    $kill_taking_resurrect = false;
	} else { // *** No kills, no turns, and too high of a level.
	    $error = 'You have no kills or turns, so you must wait to regain turns before you can return to life.';
	}
} // *** end of resurrection ***

if ($healed == 1 || $max_heal == 1) {  //If the user tried to heal themselves.
    $heal_requested = true;
    //  ***  HEALING SECTION  ***

	if ($max_heal == 1) {
	    // Sets the heal_points when the heal-all button was hit.
	    $heal_points = $startingGold;
	}
	
	$current_health = getHealth($username);

	if ($current_health >= $max_health) {
	    $error = 'You are currently at full health.';
	} else {
    	if ($current_health > 0) {  //Requires the user to be resurrected first.
    		if ($heal_points && $heal_points > 0) {  // Requires a heal number, and a positive one.
    			if ($heal_points <= $startingGold) {   //If there's enough money for the amount that they want to heal.
    				if (($current_health + $heal_points) > $max_health){  // Allows numeric healing to "round off" at the max.
    					$heal_points = ($max_health-$current_health);  //Rounds off.
    				}

    				subtractGold($username,$heal_points);
    				// Having chi increases the amount healed in all cases.
    				addHealth($username,($has_chi? round($heal_points*1.5) : $heal_points));
    				$finalHealth = getHealth($username);
    				
    				$fully_healed = ($finalHealth>=$max_health); // Let the user know whether they're fully healed.
    				
    			} else {
    			    $error = 'You do not have enough gold for that much healing.';
    			}
    		} else {
    		    $error = 'You cannot heal with zero gold.';
    		}
    	} else {
    	    $error = 'You must resurrect before you can heal.';
    	}
    }
} else if ($poisoned == 1) {	//  *** POISON SECTION ***
	$poison_cure_requested = true;

	if (getHealth($username) > 0) {
		$cost = 50;  //  the cost of curing poison is set here.

		if ($startingGold >= $cost) {
			if (getStatus($username) && $status_array['Poison']) {
				subtractGold($username,$cost);
				subtractStatus($username,POISON);
			} else {
			    $error = 'You are not ill.';
			}
		} else {
		    $error = 'You need more gold to remove poison.';
		}
	} else {
	    $error = 'You must resurrect before you can heal.';
	}
}

display_page(
	'shrine.effects.tpl'	// *** Main Template ***
	, 'Shrine'				// *** Page Title ***
	, get_certain_vars(get_defined_vars(), array())	// *** Page Variables ***
	, array(	// *** Page Options ***
		'quickstat' => 'player'
	)
);
}
?>
