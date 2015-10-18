<?php
//require_once(DB_ROOT . "PlayerVO.class.php");
//require_once(DB_ROOT . "PlayerDAO.class.php");
require_once(LIB_ROOT."control/Player.class.php");
require_once(LIB_ROOT."control/lib_attack.php");
require_once(LIB_ROOT."control/Skill.php");
require_once(LIB_ROOT."control/lib_inventory.php");
/*
 * Deals with the non-skill based attacks and stealthed attacks.
 *
 * @package combat
 * @subpackage attack
 */
$private    = true;
$alive      = true;
$page_title = "Battle Status";
$quickstat  = "player";

if ($error = init($private, $alive)) {
	display_error($error);
	die();
}


// TODO: Turn this page/system into a function to be rendered.

// *** ********* GET VARS FROM POST - OR GET ************* ***
$target      = whichever(in('target'), in('attackee'));
$duel        = (in('duel')    ? true : NULL);
$blaze       = (in('blaze')   ? true : NULL);
$deflect     = (in('deflect') ? true : NULL);
$evade       = (in('evasion') ? true : NULL);


set_setting('combat_toggles', array('duel'=>$duel, 'blaze'=>$blaze, 'deflect'=>$deflect, 'evasion'=>$evade)); // Save the combat toggled settings.


$attacker_id = self_char_id();

$attacker_obj = new Player($attacker_id);
$attacker = $attacker_obj->name();

$target_id   = get_char_id($target);

// Template vars.
$stealthed_attack = $stealth_damage = $stealth_lost = $pre_battle_stats = $rounds =
	$combat_final_results = $killed_target = $attacker_died = $bounty_result = $rewarded_ki  = $wrath_regain = false;

// *** Attack System Initialization ***
$killpoints               = 0; // *** Starting state for killpoints. ***
$attack_turns             = 1; // *** Default cost, will go to zero if an error prevents combat. ***
$required_turns           = 0;
$what                     = ""; // *** This will be the attack type string, e.g. "duel". ***
$loot                     = 0;
$simultaneousKill         = NULL; // *** Not simultaneous by default. ***
$turns_to_take            = null; // *** Even on failure take at least one turn. ***
$attack_type              = array();

if ($blaze) {
    $attack_type['blaze'] = 'blaze';
}

if ($deflect) {
    $attack_type['deflect'] = 'deflect';
}

if ($evade) {
    $attack_type['evade'] = 'evade';
}

if ($duel) {
    $attack_type['duel'] = 'duel';
} else {
	$attack_type['attack'] = 'attack';
}

$skillListObj = new Skill();

$ignores_stealth = false;

foreach ($attack_type as $type){
	$ignores_stealth = $ignores_stealth||$skillListObj->getIgnoreStealth($type);
	$required_turns += $skillListObj->getTurnCost($type);
}

// *** Attack Legal section ***
$params = array(
	'required_turns'    => $required_turns
	, 'ignores_stealth' => $ignores_stealth
);

$AttackLegal = new AttackLegal($attacker, $target, $params);
$attack_is_legal = $AttackLegal->check();
$attack_error = $AttackLegal->getError();


// *** There's a same-domain problem with this attack-legal now that it's been modified for skills ***

$target_player    = new Player($target_id);
$attacking_player = new Player($attacker_id);


// ***  MAIN BATTLE ALGORITHM  ***
if ($attack_is_legal){
	// *** Target's stats. ***
	$target_health    = $target_player->vo->health;
	$target_level     = $target_player->vo->level;
	$target_str       = $target_player->getStrength();

	// *** Attacker's stats. ***
	$attacker_health     = $attacking_player->vo->health;
	$attacker_level      = $attacking_player->vo->level;
	$attacker_turns      = $attacking_player->vo->turns;
	$attacker_str        = $attacking_player->getStrength();
	$class               = $attacking_player->vo->class;

	$starting_target_health   = $target_health;
	$starting_turns           = $attacker_turns;
	$stealthAttackDamage      = $attacker_str;
	$level_check              = $attacker_level - $target_level;

	$loot   = 0;
	$victor = null;
	$loser  = null;

	// *** ATTACKING + STEALTHED SECTION  ***
	if (!$duel && $attacking_player->hasStatus(STEALTH)) { // *** Not dueling, and attacking from stealth ***
		$attacking_player->subtractStatus(STEALTH);
		$turns_to_take = 1;

		$stealthed_attack = true;

		if (!subtractHealth($target_id, $stealthAttackDamage)) { // *** if Stealth attack of whatever damage kills target. ***
			$victor = $attacker;
			$loser  = $target;

			$gold_mod     = .1;
			$loot         = round($gold_mod * get_gold($target_id));

			$target_msg   = "DEATH: You have been killed by a stealthed ninja in combat and lost $loot gold on $today!";
			$attacker_msg = "You have killed $target in combat and taken $loot gold on $today.";

			$target_player->death();
			sendMessage("A Stealthed Ninja", $target, $target_msg);
			sendMessage($target, $attacker, $attacker_msg);
			$bounty_result = runBountyExchange($attacker, $target); // *** Determines the bounty for normal attacking. ***

			$stealth_kill = true;
		} else {	// *** if damage from stealth only hurts the target. ***
		
			$stealth_damage = true;

			sendMessage($attacker, $target, "$attacker has attacked you from the shadows for $stealthAttackDamage damage.");
		}
	} else {	// *** If the attacker is purely dueling or attacking, even if stealthed, though stealth is broken by dueling. ***
       // *** MAIN DUELING SECTION ***
       

		if ($attacking_player->hasStatus(STEALTH)) { // *** Remove their stealth if they duel instead of preventing dueling.
		    $attacking_player->subtractStatus(STEALTH);
		    
		    $stealth_lost = true;
		}

		// *** PRE-BATTLE STATS - Template Vars ***
		$pre_battle_stats = true;
		$pbs_attacker_name = $attacking_player->name();
		$pbs_attacker_str = $attacking_player->getStrength();
		$pbs_attacker_hp = $attacking_player->health();
		$pbs_target_name = $target_player->name();
		$pbs_target_str = $target_player->getStrength();
		$pbs_target_hp = $target_player->health();

		// *** BEGINNING OF MAIN BATTLE ALGORITHM ***

		$turns_counter         = $attack_turns;
		$total_target_damage   = 0;
		$total_attacker_damage = 0;
		$target_damage         = 0;
		$attacker_damage       = 0;

		// *** Combat Calculations ***
		$round = 1;
		$rounds = 0;

		while ($turns_counter > 0 && $total_target_damage < $attacker_health && $total_attacker_damage < $target_health) {
			$turns_counter -= (!$duel ? 1 : 0);// *** SWITCH BETWEEN DUELING LOOP AND SINGLE ATTACK ***

			$target_damage   = rand (1, $target_str);
			$attacker_damage = rand (1, $attacker_str);

			if ($blaze) {	// *** Blaze does double damage. ***
				$attacker_damage = $attacker_damage*2;
			}

			if ($deflect) {
				$target_damage = round($target_damage/2);
			}

			$total_target_damage   += $target_damage;
			$total_attacker_damage += $attacker_damage;
			$rounds++;	// *** Increases the number of rounds that has occured and restarts the while loop. ***

			if ($evade) {
			    // Evasion effect:
			    // Check current level of damage.
				$testValue = ($attacker_health - $total_target_damage);
                // Break off the duel/attack if less than 10% health or health is less than average of defender's strength
				if ($testValue < ($target_str*.5) || $testValue < ($attacker_health*.1)) {
					break;
				}
			}
		}
		

		// *** END OF MAIN BATTLE ALGORITHM ***

		$combat_final_results = true;
		$finalizedHealth = ($attacker_health-$total_target_damage);

		// *** RESULTING PLAYER MODIFICATION ***

		$gold_mod = 0.20;

		$turns_to_take = $required_turns;

		if ($duel) {
			$gold_mod = 0.25;
			$what     = "duel";
		}

		//  *** Let the victim know who hit them ***
		$attack_label = ($duel ? 'dueled' : 'attacked');

		$defenderHealthRemaining = subtractHealth($target_id, $total_attacker_damage);
		$attackerHealthRemaining = subtractHealth($attacker_id, $total_target_damage);

		if ($defenderHealthRemaining && $attackerHealthRemaining) {
			$combat_msg = "You have been $attack_label by $attacker at $today for $total_attacker_damage, but they got away before you could kill them!";
		} else {
			$combat_msg = "You have been $attack_label by $attacker at $today for $total_attacker_damage!";
		}

		sendMessage($attacker, $target, $combat_msg);

		if ($defenderHealthRemaining < 1 || $attackerHealthRemaining < 1) { // A kill occurred.
			if ($defenderHealthRemaining < 1) { //***  ATTACKER KILLS DEFENDER! ***
				if ($simultaneousKill = ($attackerHealthRemaining < 1)) { // *** If both died at the same time. ***
				} else {
					$victor = $attacker;
					$loser  = $target;
				}

				$killed_target = true;

				$killpoints = 1; // *** Changes killpoints from zero to one. ***

				if ($duel) {
					killpointsFromDueling();	// *** Changes killpoints amount by dueling equation. ***
					$duel_log_msg     = "$attacker has dueled $target and won $killpoints killpoints at $today.";
					if($killpoints>1 || $killpoints<0){
						// Only log duels if they're better than 1 or if they're a failure.
						sendLogOfDuel($attacker, $target, 1, $killpoints);	// *** Makes a WIN record in the dueling log. ***
					}
					if($skillListObj->hasSkill('wrath')){
						$wrath_regain = 10; // They'll retain 10 health for the kill, at the end.
					}
				}

				addKills($attacker_id, $killpoints); // *** Attacker gains their killpoints. ***
				$target_player->death();

				
				

				if (!$simultaneousKill)	{
					// This stuff only happens if you don't die also.
				
					$loot = round($gold_mod * get_gold($target_id));
					// Add the wrath health regain to the attacker.
					if(isset($wrath_regain)){
						$attacking_player->changeHealth($wrath_regain);
					}
				}

				$target_msg = "DEATH: You've been killed by $attacker and lost $loot gold on $today!";
				sendMessage($attacker, $target, $target_msg);
				
				// Stopped telling attackers when they win a duel.

				$bounty_result = runBountyExchange($attacker, $target);	// *** Determines bounty for dueling. ***
			}

			if ($attackerHealthRemaining < 1) { // *** DEFENDER KILLS ATTACKER! ***
				if ($simultaneousKill = ($attackerHealthRemaining < 1)) { // *** If both died at the same time. ***
				} else {
					$victor = $target;
					$loser  = $attacker;
				}
				
				$attacker_died = true;
				
				$defenderKillpoints = 1;

				if ($duel) {	// *** if they were dueling when they died ***
					$duel_log_msg     = "$attacker has dueled $target and lost at $today.";
					sendMessage("SysMsg", "SysMsg", $duel_log_msg);
					sendLogOfDuel($attacker, $target, 0, $killpoints);	// *** Makes a loss in the duel log. ***
				}

				addKills($target_id, $defenderKillpoints);	// *** Adds a kill for the defender. ***
				$attacking_player->death();

				if (!$simultaneousKill) {
					$loot = round($gold_mod * get_gold($attacker_id));//Loot for defender if he lives.
				}

				$target_msg = "You have killed $attacker in combat and taken $loot gold on $today.";

				$attacker_msg = "DEATH: You've been killed by $target and lost $loot gold on $today!";

				sendMessage($attacker, $target, $target_msg);
				sendMessage($target, $attacker, $attacker_msg);

			}
		}

		// *** END MAIN ATTACK AND DUELING SECTION ***
	}

	if ($loot) {
		add_gold(get_char_id($victor), $loot);
		subtract_gold(get_char_id($loser), $loot);
	}
	

	if($rounds>4){
		// Even matched battle!  Reward some ki to the attacker, even if they die.
		change_ki($attacker_id, 1); // Award Ki.
		$rewarded_ki = 1;
	}
	
}

// *** Take away at least one turn even on attacks that fail. ***
if ($turns_to_take < 1) {
	$turns_to_take = 1;
}

$ending_turns = subtractTurns($attacker_id, $turns_to_take);

//  ***  START ACTION OVER AGAIN SECTION ***

$attack_again = false;
if (isset($target)) {
    $attacker_health_snapshot = getHealth($attacker_id);
    $defender_health_snapshot = getHealth($target_id);
	if ($AttackLegal && $attacker_health_snapshot > 0 && $defender_health_snapshot > 0) {	// *** After any partial attack. ***
		$attack_again = true;
	}
}

$target_ending_health = $target_player->health();
$target_ending_health_percent = $target_player->health_percent();
$target_name = $target_player->name();

display_page('attack_mod.tpl', 'Battle Status', get_defined_vars());
