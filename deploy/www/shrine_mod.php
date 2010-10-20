<?php
require_once(LIB_ROOT."specific/lib_inventory.php");
$private   = false;
$alive     = false;

if ($error = init($private, $alive)) {
	display_error($error);
} else {

include(OBJ_ROOT."Skill.php");
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
$heal_points        = (in('heal_points') ? intval(in('heal_points')) : null);  // The pointwise healing method.
$freeResLevelLimit  = 6;
$freeResKillLimit   = 25;
$lostTurns          = 10; // *** Default turns lost when the player has no kills.
$has_chi            = $skillsListObj->hasSkill('Chi'); // Extra healing benefits from chi, by level.
$error              = null;
$max_health         = determine_max_health($level);
$fully_healed       = false;

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
	$has_hidden_res        = $skillsListObj->hasSkill('hidden resurrect');

	if ($startingHealth > 0) {
		$error = 'You are not dead.';
	} else {
		if ($startingKills > 1 || $freeResurrection) {	// If you're dead, and a newbie, or dead and have kills.
			$turn_taking_resurrect = false; // Template display variable.
			$kill_taking_resurrect = false;
			//  *** FREE RESURRECTION DETERMINATION ***

			if (!$freeResurrection) { // 1 kill point cost for resurrection above level 5 or 25 kills.
				$player->vo->kills = subtractKills($player->vo->uname, 1);
				$kill_taking_resurrect = true;
			}
		} elseif ($startingTurns > 0) { // Dead and no killpoints left, and not a newbie.
			$turn_taking_resurrect = true; // Template display variable.
		    $kill_taking_resurrect = false;

			if ($startingTurns < $lostTurns && $startingTurns > 0) { // *** From 9 to 1 turns.
				$lostTurns = $startingTurns;
			}

			$final_turns = $player->vo->turns = subtractTurns($player->vo->uname, $lostTurns); // *** Takes away necessary turns.
		} else { // *** No kills, no turns, and too high of a level.
	    	$error = 'You have no kills or turns, so you must wait to regain turns before you can return to life.';
		}

		if ($kill_taking_resurrect || $turn_taking_resurrect || $freeResurrection) {
			$player->death();

			if ($kill_taking_resurrect) {
				// *** FREE STEALTHING FOR ANYONE WITH HIDDEN RESURRECT SKILL UPON NON-FREE RESURRECTION
				$skillsListObj = new Skill();

				if (!$freeResurrection && $has_hidden_res) {
					$player->addStatus(STEALTH);
				}

				$returning_health = ($has_chi ? 150 : 100);
			} else {
				$returning_health = 100;
			}

			$finalHealth = $player->vo->health = setHealth($player->vo->uname, $returning_health);
			$final_turns = $player->vo->turns;
			$final_kills = $player->vo->kills;
		}
	}
} // *** end of resurrection ***

if ($healed == 1 || $max_heal == 1) {  //If the user tried to heal themselves.
	$heal_requested = true;
	//  ***  HEALING SECTION  ***

	// *** Having chi decreases the cost of healing by 50% ***
	$costOfHealPoint = ($has_chi ? .5 : 1);

	if ($max_heal == 1) {
	    // Sets the heal_points when the heal-all button was hit.
	    $heal_points = floor($startingGold/$costOfHealPoint);
	}

	$current_health = $player->vo->health;
	$current_damage = $max_health - $current_health;
	$heal_points    = min($heal_points, $current_damage); // *** Cannot heal higher than max ***
	$total_cost     = ceil($heal_points*$costOfHealPoint);

	if ($current_health >= $max_health) {
	    $error = 'You are currently at full health.';
	} else {
		if ($current_health > 0) {  // *** Requires the user to be resurrected first. ***
			if ($heal_points && $heal_points > 0) {  // *** Requires a heal number, and a positive one. ***
				if ($total_cost <= $startingGold) {   // *** If there's enough money for the amount that they want to heal. ***
					subtract_gold($player->id(), $total_cost);
					$player->vo->health = $finalHealth = addHealth($player->vo->uname, $heal_points);

					$fully_healed = ($finalHealth >= $max_health); // ** Test if user is fully healed. ***
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

	if ($player->vo->health > 0) {
		$cost = 50;  //  the cost of curing poison is set here.

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
