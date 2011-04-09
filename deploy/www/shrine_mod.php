<?php
require_once(LIB_ROOT."control/lib_inventory.php");
$private   = false;
$alive     = false;

if ($error = init($private, $alive)) {
	display_error($error);
} else {

require_once(LIB_ROOT."control/Skill.php");
$skillsListObj = new Skill();

$healed             = in('healed');
$poisoned           = in('poisoned');
$restore            = (int)in('restore');
$max_heal           = in('max_heal');
$heal_and_resurrect = in('heal_and_resurrect');
$cured              = false;
$player             = new Player(get_char_id());
$finalHealth        =
$startingHealth     = $player->vo->health;
$userLevel          = $player->vo->level;
$startingGold       = $player->vo->gold;
$startingKills      = $player->vo->kills;
$startingTurns      = $player->vo->turns;
$level              = $player->vo->level;
$max_health         = $player->max_health();
$heal_points        = (in('heal_points') ? intval(in('heal_points')) : null);  // The pointwise healing method.
$freeResLevelLimit  = 6;
$freeResKillLimit   = 25;
$lostTurns          = 10; // *** Default turns lost when the player has no kills.
$has_chi            = $skillsListObj->hasSkill('Chi'); // Extra healing benefits from chi, by level.
$error              = null;
$fully_healed       = false;
$final_kills        = $startingKills; // No kills taken unless they're taken.

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

if ($restore === 1) {	//  *** RESURRECTION SECTION ***
	$resurrect_requested   = true;
	$turn_taking_resurrect = false;
	$kill_taking_resurrect = true;
	$has_hidden_res        = false;

	if ($startingHealth > 0) {
		$error = 'You are not dead.';
	} else {
	
	
	
		// *********  Determine the type of Resurrection *********
	
	
		if ($startingKills > 1 || $freeResurrection) {	// If you're dead, and a newbie, or dead and have kills.
			$turn_taking_resurrect = false; // Template display variable.
			$kill_taking_resurrect = false;
			//  *** FREE RESURRECTION DETERMINATION ***

			if (!$freeResurrection) { // 1 kill point cost for resurrection above level 5 or 25 kills.
				$player->vo->kills = subtractKills($player->vo->player_id, 1);
				$kill_taking_resurrect = true;
			}
		} elseif ($startingTurns > 0) { // Dead and no killpoints left, and not a newbie.
			$turn_taking_resurrect = true; // Template display variable.
		    $kill_taking_resurrect = false;

			if ($startingTurns < $lostTurns && $startingTurns > 0) { // *** From 9 to 1 turns.
				$lostTurns = $startingTurns;
			}

			$final_turns = $player->vo->turns = subtractTurns($player->vo->player_id, $lostTurns); // *** Takes away necessary turns.
		} else { // *** No kills, no turns, and too high of a level.
	    	$error = 'You have no kills or turns, so you must wait to regain turns before you can return to life.';
		}



		// ********** Peform resurrect effects for worthy characters **************
		
		
		if ($kill_taking_resurrect || $turn_taking_resurrect || $freeResurrection) {
			$player->death();
			
			$base_res = 100;
			$chi_multiplier = 3;
			
			$base_health= ($has_chi? $base_res*$chi_multiplier : $base_res);
			// Chi triples the base health.

			if ($kill_taking_resurrect) {
				// *** FREE STEALTHING FOR ANYONE WITH HIDDEN RESURRECT SKILL UPON NON-FREE RESURRECTION
				$skillsListObj = new Skill();
				
				$has_hidden_res = $skillsListObj->hasSkill('hidden resurrect');
				if ($has_hidden_res) {
					$player->addStatus(STEALTH);
				}
				
				// Resurrecting gives health benefits for higher levels.
				$returning_health = $base_health + (($level-1)*5);
			} else {  // Non-standard resurrect costs give a substandard result.
				$returning_health = $base_health;
			}
			$returning_health = max($returning_health, $player->max_health()); // Can't heal above max_health.
			
			$finalHealth = $player->vo->health = $player->heal($returning_health);
			$final_turns = $player->vo->turns;
			$final_kills = $player->vo->kills;
		}
	}
} // *** end of resurrection ***



if ($healed == 1 || $max_heal == 1) {  //If the player wants to heal themselves
	$heal_requested = true;
	
	//  ***  HEALING SECTION  ***

	// *** Having chi decreases the cost of healing by half ***
	$costOfHealPoint = ($has_chi ? 0.5 : 1);

	if ($max_heal == 1) {
	    // Sets the heal_points when the heal-all button was hit.
	    $heal_points = (int) (2*$startingGold)/(2*$costOfHealPoint);
	}

	// Check hurt remaining after resurrection.
	$hurt = $player->hurt_by();
	$current_health = $player->vo->health;
	$heal_points    = min($heal_points, $hurt); // *** Cannot heal higher than request or hurt ***
	$total_cost     = ceil($heal_points*$costOfHealPoint);

	if (!$hurt) {
	    $error = 'You are at full health.';
	} else {
		if ($current_health > 0) {  // *** Requires the user to be resurrected first. ***
			if ($heal_points && $heal_points > 0) {  // *** Requires a heal number, and a positive one. ***
				if ($total_cost <= $startingGold) {   // *** If there's enough money for the amount that they want to heal. ***
					subtract_gold($player->id(), $total_cost);
					$player->vo->health = $finalHealth = $player->heal($heal_points);

					$fully_healed = !$player->hurt_by(); // ** Test if user is fully healed. ***
				} else {
					$error = 'You do not have enough gold for that much healing.';
				}
			} else if ($restore !== 1) {	// *** Only display this error if they have not requested a ressurect. ***
				$error = 'You cannot heal with zero gold.';
			}
		} else {
			$error = 'You must resurrect before you can heal.';
		}
	}
} else if ($poisoned == 1) {	//  *** POISON SECTION ***
	$poison_cure_requested = true;

	if ($player->health() > 0) {
		$cost = 100;  //  the cost of curing poison is set here.

		if ($startingGold >= $cost) {
			if ($player->hasStatus(POISON)) {
				subtract_gold($player->id(), $cost);
				$player->subtractStatus(POISON);
				$cured = true;
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

$health_percent = $player? $player->health_percent() : null; // Get the final health percent for display.

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
