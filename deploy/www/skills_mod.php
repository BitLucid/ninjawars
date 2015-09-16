<?php
require_once(LIB_ROOT.'control/lib_inventory.php');
require_once(LIB_ROOT.'control/Skill.php');
require_once(LIB_ROOT.'control/CloneKill.class.php');
/*
 * Deals with the skill based attacks, and status effects.
 *
 * @package combat
 * @subpackage skill
 */
$private    = true;
$alive      = true;



//$are_clones = characters_are_linked(get_char_id('zig'), get_char_id('zag'));
//debug($are_clones, account_info(get_char_account_id(get_char_id('zig'))), account_info(get_char_account_id(get_char_id('zag'))));die();


if ($error = init($private, $alive)) {
	display_error($error);
	die();
}

// Template vars.
$display_sight_table = $generic_skill_result_message = $generic_state_change = $killed_target = 
	$loot = $added_bounty = $bounty = $suicided = $destealthed = null;

//Get filtered info from input.
$target  = in('target');
$command = in('command');
$stealth = in('stealth');

$skillListObj = new Skill();
$poisonMaximum   = 100; // *** Before level-based addition.
$poisonMinimum   = 1;
$poisonTurnCost  = $skillListObj->getTurnCost('poison touch'); // wut
$turn_cost       = $skillListObj->getTurnCost(strtolower($command));
$ignores_stealth = $skillListObj->getIgnoreStealth($command);
$self_use        = $skillListObj->getSelfUse($command);
$use_on_target   = $skillListObj->getUsableOnTarget($command);
$ki_cost 		 = 0; // Ki taken during use.
$reuse 			 = true;  // Able to reuse the skill.
$today = date("F j, Y, g:i a");

// Check whether the user actually has the needed skill.
$has_skill = $skillListObj->hasSkill($command);

$starting_turn_cost = $turn_cost;
assert($turn_cost>=0);
$turns_to_take = null;  // *** Even on failure take at least one turn.
$char_id = self_char_id();

$player = new Player($char_id);

if ($target != '' && $target != $player->player_id) {
	$target = new Player($target);
	$target_id = $target->id();
	$return_to_target = true;
} else {
	// Use the skill on himself.
	$return_to_target = false;
	$target    = $player;
	$target_id = null;
}

$user_ip         = get_account_ip();
$class           = $player->vo->class;
$covert          = false;
$victim_alive    = true;
$attacker_id     = $username;
$attacker_char_id = self_char_id();
$starting_turns  = $player->vo->turns;
$ending_turns    = null;

$level_check  = $player->vo->level - $target->vo->level;

if ($player->hasStatus(STEALTH)) {
	$attacker_id = 'A Stealthed Ninja';
}

$use_attack_legal = true;

if ($command == 'Clone Kill' || $command == 'Harmonize') {
	$has_skill = true;
	$use_attack_legal = false;
	$attack_allowed = true;
	$attack_error = null;
	$covert = true;
} else {
	// *** Checks the skill use legality, as long as the target isn't self.
	$params         = array('required_turns'=>$turn_cost, 'ignores_stealth'=>$ignores_stealth, 'self_use'=>$self_use);
	$AttackLegal    = new AttackLegal($player->player_id, $target->player_id, $params);
	$attack_allowed = $AttackLegal->check();
	$attack_error   = $AttackLegal->getError();
}

if (!$attack_error) { // Only bother to check for other errors if there aren't some already.
	if (!$has_skill || $class == '' || $command == '') {
		// Set the attack error to display that that skill wasn't available.
		$attack_error = 'You do not have the requested skill.';
	} elseif ($starting_turns < $turn_cost) {
		$turn_cost = 0;
		$attack_error = "You do not have enough turns to use $command.";
	}
}

// Strip down the player info to get the sight data.
function pull_sight_data($target_id) {
	$data = char_info($target_id);
	// Strip all fields but those allowed.
	$allowed = array('Name'=>'uname', 'Class'=>'class', 'Level'=>'level', 'Turns'=>'turns', 'Strength'=>'strength', 'Speed'=>'speed', 'Stamina'=>'stamina', 'Ki'=>'ki', 'Gold'=>'gold', 'Kills'=>'kills');
	$res = array();

	
	foreach ($allowed as $header => $field) {
		$res[$header] = $data[$field];
	}

	return $res;
}

if (!$attack_error) { // Nothing to prevent the attack from happening.
	// Initial attack conditions are alright.
	$result = '';
	
	
	if ($command == 'Sight') {
		$covert = true;

		$sight_data = pull_sight_data($target_id);
			
		$display_sight_table = true;
	} elseif ($command == 'Steal') {
		$covert = true;

		$gold_decrease = rand(1, 50);
		$target_gold   = $target->vo->gold;
		$gold_decrease = ($target_gold < $gold_decrease ? $target_gold : $gold_decrease);

		add_gold($char_id, $gold_decrease); // *** This one actually adds the value.
		subtract_gold($target->id(), $gold_decrease); // *** Subtracts whatever positive value is put in.

		$msg = "You have had pick pocket cast on you for $gold_decrease by $attacker_id at $today";
		send_event($attacker_char_id, $target->id(), $msg);
		
		$generic_skill_result_message = "You have stolen $gold_decrease gold from __TARGET__!";
	} else if ($command == 'Unstealth') {
		$state = 'unstealthed';

		if ($target->hasStatus(STEALTH)) {
			$target->subtractStatus(STEALTH);
			$generic_state_change = "You are now $state.";
		} else {
			$turn_cost = 0;
			$generic_state_change = "__TARGET__ is already $state.";
		}
	} else if ($command == 'Stealth') {
		$covert     = true;
		$state      = 'stealthed';

		if (!$target->hasStatus(STEALTH)) {
			$target->addStatus(STEALTH);
			$generic_state_change = "__TARGET__ is now $state.";
		} else {
			$turn_cost = 0;
			$generic_state_change = "__TARGET__ is already $state.";
		}
	} else if ($command == 'Kampo') {
		$covert = true;

		// *** Get Special Items From Inventory ***
		$user_id = self_char_id();
		$root_item_type = 7;
        $itemCount = query_item('SELECT sum(amount) AS c FROM inventory WHERE owner = :owner AND item_type = :type GROUP BY item_type',
                array(':owner'=>$user_id, ':type'=>$herb_item_type));
        $turn_cost = min($itemCount, $starting_turns-1, 2); // Costs 1 or two depending on the number of items.
		if ($turn_cost && $itemCount > 0) {	// *** If special item count > 0 ***
			remove_item($user_id, 'ginsengroot', $itemCount);
			add_item($user_id, 'tigersalve', $itemCount);
			
			$generic_skill_result_message = 'With intense focus you grind the '.$itemsCount.' roots into potent formulas.';
		} else { // *** no special items, give error message ***
			$turn_cost = 0;
			$generic_skill_result_message = 'You do not have the necessary ginsengroots or energy to create any Kampo formulas.';
		}
	} else if ($command == 'Poison Touch') {
		$covert = true;

		$target->addStatus(POISON);
		$target->addStatus(WEAKENED); // Weakness kills strength.

		$target_damage = rand($poisonMinimum, $poisonMaximum);

		$victim_alive = $target->subtractHealth($target_damage);
		$generic_state_change = "__TARGET__ has been poisoned!";
		$generic_skill_result_message = "__TARGET__ has taken $target_damage damage!";

		$msg = "You have been poisoned by $attacker_id at $today";
		send_event($attacker_char_id, $target->id(), $msg);
	} elseif ($command == 'Fire Bolt') {
		$target_damage = (5 * (ceil($player->vo->level / 3)) + rand(1, $player->getStrength()));

		$generic_skill_result_message = "__TARGET__ has taken $target_damage damage!";

		if ($victim_alive = subtractHealth($target->vo->player_id, $target_damage)) {
			$attacker_id  = $username;
		}

		$msg = "You have had fire bolt cast on you by $attacker_id at $today";
		send_event($attacker_char_id, $target->id(), $msg);
	} else if ($command == 'Heal' || $command == 'Harmonize') {
	
		// This is the starting template for self-use commands, eventually it'll be all refactored.
	
		
		$harmonize = false;
		if($command == 'Harmonize'){
			$harmonize = true;
		}
		

		
		


	 
	    $hurt = $target->hurt_by(); // Check how much the TARGET is hurt (not the originator, necessarily).
	    // Check that the target is not already status healing.
	    if ($target->hasStatus(HEALING) && !$player->isAdmin()) {
	        $turn_cost = 0;
	        $generic_state_change = '__TARGET__ is already under a healing aura.';
		} elseif ($hurt < 1) {
			$turn_cost = 0;
			$generic_skill_result_message = '__TARGET__ is already fully healed.';
		} else {
			if(!$harmonize){
				$heal_per_level = 10; // For standard heal.
				// Heal at most either: hurt, or heal capacity.
				$healed_by = min($hurt, $player->level()*$heal_per_level);
				// Call the heal method on the targetted player object.
				$new_health = $target->heal($healed_by);
			} else {
				// Harmonize some chakra!



// Use up some ki to heal yourself.
function harmonize_chakra($char_obj){
	$ki = $char_obj->ki();
	$healed_by = 0;
	$hurt = $char_obj->hurt_by();
	if($hurt > 0){
		// If there's anything to heal, try.
		// Heal to whichever is lowest, ki, hurt, or 300.
		$heal_for = min(100, $hurt, $ki);
		// Subtract the ki used for healing.
		$char_obj->subtract_ki((int)$heal_for);
		$char_obj->heal($heal_for);
		$healed_by = $heal_for;
	}
	return $healed_by;
}



				$start_health = $player->health();
				// Harmonize those chakra!
				$healed_by = harmonize_chakra($player);
				$new_health = $healed_by + $start_health;
				$ki_cost = $healed_by;
			}
		    
		    $target->addStatus(HEALING);
		    $generic_skill_result_message = "__TARGET__ healed by $healed_by to $new_health.";

		    if ($target->id() != $player->id())  {
				send_event($attacker_char_id, $target->id(), "You have been healed by $attacker_id at $today for $healed_by.");
			}
		}
	} else if ($command == 'Ice Bolt') {
		if (!$target->hasStatus(SLOW)) {
			if ($target->vo->turns >= 10) {
				$turns_decrease = rand(1, 5);
				subtractTurns($target->vo->player_id, $turns_decrease);
				// Changed ice bolt to kill stealth.
				$target->subtractStatus(STEALTH);
				$target->addStatus(SLOW);

				$msg = "Ice bolt cast on you by $attacker_id at $today, your turns have been reduced by $turns_decrease.";
				send_event($attacker_char_id, $target->id(), $msg);

				$generic_skill_result_message = "__TARGET__'s turns reduced by $turns_decrease!";
			} else {
				$turn_cost = 0;
				$generic_skill_result_message = "__TARGET__ does not have enough turns for you to take.";
			}
		} else {
			$turn_cost = 0;
			$generic_skill_result_message = '__TARGET__ is already iced.';
		}
	} else if ($command == 'Cold Steal') {
		if (!$target->hasStatus(SLOW)) {
			$critical_failure = rand(1, 100);

			if ($critical_failure > 7) {// *** If the critical failure rate wasn't hit.
				if ($target->vo->turns >= 10) {
					$turns_decrease = rand(2, 7);

					subtractTurns($target->vo->player_id, $turns_decrease);
					$target->addStatus(SLOW);
					addTurns($char_id, $turns_decrease);

					$msg = "You have had Cold Steal cast on you for $turns_decrease by $attacker_id at $today";
					send_event($attacker_char_id, $target->id(), $msg);

					$generic_skill_result_message = "You cast Cold Steal on __TARGET__ and take $turns_decrease turns.";
				} else {
					$turn_cost = 0;
					$generic_skill_result_message = '__TARGET__ did not have enough turns to give you.';
				}
			} else { // *** CRITICAL FAILURE !!
				$player->addStatus(FROZEN);

				$unfreeze_time = date('F j, Y, g:i a', mktime(date('G')+1, 0, 0, date('m'), date('d'), date('Y')));

				$failure_msg = "You have experienced a critical failure while using Cold Steal on $today. You will be unfrozen on $unfreeze_time";
				sendMessage("SysMsg", $username, $failure_msg);
				$generic_skill_result_message = "Cold Steal has backfired! You are frozen until $unfreeze_time!";
			}
		} else {
			$turn_cost = 0;
			$generic_skill_result_message = '__TARGET__ is already iced.';
		}
	} else if ($command == 'Clone Kill') {

	
		// Obliterates the turns and the health of similar accounts that get clone killed.
		$reuse = false; // Don't give a reuse link.
	
		$clone1 = in('clone1');
		$clone2 = in('clone2');
		
		$clone_1_id = get_char_id($clone1);
		$clone_2_id = get_char_id($clone2);
		$clones = false;

		if (!$clone_1_id || !$clone_2_id) {
			$not_a_ninja = $clone1;

			if (!$clone_2_id) {
				$not_a_ninja = $clone2;
			}

			$generic_skill_result_message = "There is no such ninja as $not_a_ninja.";
		} elseif ($clone_1_id == $clone_2_id) {
			$generic_skill_result_message = '__TARGET__ is just the same ninja, so not the same thing as a clone at all.';
		} elseif ($clone_1_id == $char_id || $clone_2_id == $char_id) {
			$generic_skill_result_message = 'You cannot clone kill yourself.';
		} else {
			// The two potential clones will be obliterated immediately if the criteria are met in CloneKill.
			$kill_or_fail = CloneKill::kill($player, new Player($clone_1_id), new Player($clone_2_id));
			if($kill_or_fail !== false){
				$generic_skill_result_message = $kill_or_fail;
			} else {
				$generic_skill_result_message = "Those two ninja don't seem to be clones.";
			}
			/*
			$are_clones = characters_are_linked($clone_1_id, $clone_2_id);

			if ($are_clones) {
				$clone_char = new Player($clone_1_id);
				$clone_char_2 = new Player($clone_2_id);
				$clone_char_health = $clone_char->health();
				$clone_char_2_health = $clone_char_2->health();
				$clone_char_turns = $clone_char->turns();
				$clone_char_2_turns = $clone_char_2->turns();
				$clone_char->death();
				$clone_char->changeTurns(-1*$clone_char->turns());
				$clone_char_2->death();
				$clone_char_2->changeTurns(-1*$clone_char_2->turns());
				$generic_skill_result_message = "You obliterate the clone {$clone_char->name()} for $clone_char_health health, $clone_char_turns turns
					 and the clone {$clone_char_2->name()} for $clone_char_2_health health, $clone_char_2_turns turns.";
				send_event($char_id, $clone_1_id, "You and {$clone_char_2->name()} were Clone Killed at $today.");
				send_event($char_id, $clone_2_id, "You and {$clone_char->name()} were Clone Killed at $today.");
			} else {
				$generic_skill_result_message = "Those two ninja don't seem to be clones.";
			}
			*/
		}
	}
	
	
	

	if (!$victim_alive) { // Someone died.
		if ($target->player_id == $player->player_id) { // Attacker killed themself.
			$loot = 0;
			$suicided = true;
		} else { // Attacker killed someone else.
			$killed_target = true;
			$gold_mod = 0.15;
			$loot     = round($gold_mod * get_gold($target->id()));

			subtract_gold($target->id(), $loot);
			add_gold($char_id, $loot);

			addKills($char_id, 1);

			$added_bounty = floor($level_check / 5);

			if ($added_bounty > 0) {
				addBounty($char_id, ($added_bounty * 25));
			} else { // Can only receive bounty if you're not getting it on your own head.
				if ($bounty = rewardBounty($char_id, $target->vo->player_id)) {

					$bounty_msg = "You have valiantly slain the wanted criminal, $target! For your efforts, you have been awarded $bounty gold!";
					sendMessage('Village Doshin', $username, $bounty_msg);
				}
			}

			$target_message = "$attacker_id has killed you with $command on $today and taken $loot gold.";
			send_event($attacker_char_id, $target->id(), $target_message);

			$attacker_message = "You have killed $target with $command on $today and taken $loot gold.";
			sendMessage($target->vo->uname, $username, $attacker_message);
		}
	}

	$turns_to_take = $turns_to_take - $turn_cost;

	if (!$covert && $player->hasStatus(STEALTH)) {
		$player->subtractStatus(STEALTH);
		$destealthed = true;
	}
	
	
} // End of the skill use SUCCESS block.

$ending_turns = changeTurns($attacker_char_id, $turns_to_take); // Take the skill use cost.

$target_ending_health = $target->health();
$target_ending_health_percent = $target->health_percent();
$target_name = $target->name();

display_page(
	'skills_mod.tpl'
	, 'Skill Effect'
	, get_defined_vars()
	, array(
		'quickstat' => 'player'
	)
);
