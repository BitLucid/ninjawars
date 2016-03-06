<?php
require_once(LIB_ROOT.'control/lib_inventory.php');
require_once(LIB_ROOT.'control/Skill.php');
require_once(LIB_ROOT.'control/CloneKill.class.php');

use NinjaWars\core\control\AttackLegal;

/*
 * Deals with the skill based attacks, and status effects.
 *
 * @package combat
 * @subpackage skill
 */
$private    = true;
$alive      = true;

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

$skillListObj    = new Skill();
$poisonMaximum   = 100; // *** Before level-based addition.
$poisonMinimum   = 1;
$poisonTurnCost  = $skillListObj->getTurnCost('poison touch'); // wut
$turn_cost       = $skillListObj->getTurnCost(strtolower($command));
$ignores_stealth = $skillListObj->getIgnoreStealth($command);
$self_use        = $skillListObj->getSelfUse($command);
$use_on_target   = $skillListObj->getUsableOnTarget($command);
$ki_cost 		 = 0; // Ki taken during use.
$reuse 			 = true;  // Able to reuse the skill.
$today           = date("F j, Y, g:i a");

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

$covert           = false;
$victim_alive     = true;
$attacker_id      = $player->name();
$attacker_char_id = self_char_id();
$starting_turns   = $player->vo->turns;
$ending_turns     = null;

$level_check  = $player->vo->level - $target->vo->level;

if ($player->hasStatus(STEALTH)) {
	$attacker_id = 'A Stealthed Ninja';
}

$use_attack_legal = true;

if ($command == 'Clone Kill' || $command == 'Harmonize') {
	$has_skill        = true;
	$use_attack_legal = false;
	$attack_allowed   = true;
	$attack_error     = null;
	$covert           = true;
} else {
	// *** Checks the skill use legality, as long as the target isn't self.
    $params = [
        'required_turns'  => $turn_cost,
        'ignores_stealth' => $ignores_stealth,
        'self_use'        => $self_use
    ];

	$AttackLegal    = new AttackLegal($player, $target, $params);
	$attack_allowed = $AttackLegal->check();
	$attack_error   = $AttackLegal->getError();
}

if (!$attack_error) { // Only bother to check for other errors if there aren't some already.
	if (!$has_skill || $command == '') {
		// Set the attack error to display that that skill wasn't available.
		$attack_error = 'You do not have the requested skill.';
	} elseif ($starting_turns < $turn_cost) {
		$turn_cost = 0;
		$attack_error = "You do not have enough turns to use $command.";
	}
}

// Strip down the player info to get the sight data.
function pull_sight_data($target) {
	$data = $target->dataWithClan();
	// Strip all fields but those allowed.
    $allowed = [
        'Name'     => 'uname',
        'Class'    => 'class',
        'Level'    => 'level',
        'Turns'    => 'turns',
        'Strength' => 'strength',
        'Speed'    => 'speed',
        'Stamina'  => 'stamina',
        'Ki'       => 'ki',
        'Gold'     => 'gold',
        'Kills'    => 'kills',
    ];

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

		$sight_data = pull_sight_data($target);

		$display_sight_table = true;
	} elseif ($command == 'Steal') {
		$covert = true;

		$gold_decrease = min($target->gold(), rand(5, 50));

		add_gold($char_id, $gold_decrease); // *** This one actually adds the value.
		subtract_gold($target->id(), $gold_decrease); // *** Subtracts whatever positive value is put in.

		$msg = "$attacker_id stole $gold_decrease gold from you.";
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
                array(':owner'=>$user_id, ':type'=>$root_item_type));
        $turn_cost = min($itemCount, $starting_turns-1, 2); // Costs 1 or two depending on the number of items.
		if ($turn_cost && $itemCount > 0) {	// *** If special item count > 0 ***
			removeItem($user_id, 'ginsengroot', $itemCount);
			add_item($user_id, 'tigersalve', $itemCount);

			$generic_skill_result_message = 'With intense focus you grind the '.$itemCount.' roots into potent formulas.';
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

		$msg = "You have been poisoned by $attacker_id";
		send_event($attacker_char_id, $target->id(), $msg);
	} elseif ($command == 'Fire Bolt') {
		$target_damage = (5 * (ceil($player->level() / 3)) + rand(1, $player->getStrength()));

		$generic_skill_result_message = "__TARGET__ has taken $target_damage damage!";

		$victim_alive = $target->harm($target_damage);

		$msg = "You have had fire bolt cast on you by ".$player->name();
		send_event($player->id(), $target->id(), $msg);
	} else if ($command == 'Heal' || $command == 'Harmonize') {
		// This is the starting template for self-use commands, eventually it'll be all refactored.
		$harmonize = false;

		if ($command == 'Harmonize') {
			$harmonize = true;
		}

	    $hurt = $target->is_hurt_by(); // Check how much the TARGET is hurt (not the originator, necessarily).
	    // Check that the target is not already status healing.
	    if ($target->hasStatus(HEALING) && !$player->isAdmin()) {
	        $turn_cost = 0;
	        $generic_state_change = '__TARGET__ is already under a healing aura.';
		} elseif ($hurt < 1) {
			$turn_cost = 0;
			$generic_skill_result_message = '__TARGET__ is already fully healed.';
		} else {
			if(!$harmonize){
				$original_health = $target->health();
				$heal_per_level = 10; // For standard heal.
				$heal_points = $player->level()*$heal_per_level;
				$new_health = $target->heal($heal_points); // Won't heal more than possible
				$healed_by = $new_health - $original_health;
			} else {
				// Harmonize some chakra!

				// Use up some ki to heal yourself.
				function harmonize_chakra(Player $char){
					// Heal at most 100 or ki available or hurt by AND at least 0
					$heal_for = (int) max(0, min(100, $char->is_hurt_by(), $char->ki()));
					if($heal_for > 0){
						// If there's anything to heal, try.


						// Subtract the ki used for healing.
						$char->heal($heal_for);
						$char->set_ki($char->ki() - $heal_for);
						$char->save();
					}
					return $char;
				}

				$start_health = $player->health();
				// Harmonize those chakra!
				$player = harmonize_chakra($player);
				$healed_by = $player->health() - $start_health;
				$ki_cost = $healed_by;
			}

		    $target->addStatus(HEALING);
		    $generic_skill_result_message = "__TARGET__ healed by $healed_by to ".$target->health().".";

		    if ($target->id() != $player->id())  {
				send_event($attacker_char_id, $target->id(), "You have been healed by $attacker_id for $healed_by.");
			}
		}
	} else if ($command == 'Ice Bolt') {
		if (!$target->hasStatus(SLOW)) {
			if ($target->vo->turns >= 10) {
				$turns_decrease = rand(1, 5);
				$target->subtractTurns($turns_decrease);
				// Changed ice bolt to kill stealth.
				$target->subtractStatus(STEALTH);
				$target->addStatus(SLOW);

				$msg = "Ice bolt cast on you by $attacker_id, your turns have been reduced by $turns_decrease.";
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

					$target->subtractTurns($turns_decrease);
					$target->addStatus(SLOW);
					$player->changeTurns(abs($turns_decrease));

					$msg = "You have had Cold Steal cast on you for $turns_decrease by $attacker_id";
					send_event($attacker_char_id, $target->id(), $msg);

					$generic_skill_result_message = "You cast Cold Steal on __TARGET__ and take $turns_decrease turns.";
				} else {
					$turn_cost = 0;
					$generic_skill_result_message = '__TARGET__ did not have enough turns to give you.';
				}
			} else { // *** CRITICAL FAILURE !!
				$player->addStatus(FROZEN);

				$unfreeze_time = date('F j, Y, g:i a', mktime(date('G')+1, 0, 0, date('m'), date('d'), date('Y')));

				$failure_msg = "You have experienced a critical failure while using Cold Steal. You will be unfrozen on $unfreeze_time";
				sendMessage("SysMsg", $player->name(), $failure_msg);
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
		}
	}

	if (!$victim_alive) { // Someone died.
		if ($target->player_id == $player->player_id) { // Attacker killed themself.
			$loot = 0;
			$suicided = true;
		} else { // Attacker killed someone else.
			$killed_target = true;
			$gold_mod = 0.15;
			$loot     = floor($gold_mod * $target->gold());

			$player->set_gold($player->gold() + $loot);
			$target->set_gold($target->gold() - $loot);

			$player->addKills(1);

			$added_bounty = floor($level_check / 5);

            // Can only receive bounty if you're not getting it on your own head
			if ($added_bounty > 0) {
				$player->set_bounty($player->bounty + ($added_bounty * 25));
            } else if ($target->bounty > 0) {
                $player->set_gold($player->gold + $target->bounty);
                $target->set_bounty(0);

                $bounty_msg = "You have valiantly slain the wanted criminal, $target! For your efforts, you have been awarded $bounty gold!";
                sendMessage('Village Doshin', $player->name(), $bounty_msg);
			}

			$target_message = "$attacker_id has killed you with $command and taken $loot gold.";
			send_event($attacker_char_id, $target->id(), $target_message);

			$attacker_message = "You have killed $target with $command and taken $loot gold.";
			sendMessage($target->vo->uname, $player->name(), $attacker_message);

			$target->save();
			$player->save();
		}
	}

	$turns_to_take = $turns_to_take - $turn_cost;

	if (!$covert && $player->hasStatus(STEALTH)) {
		$player->subtractStatus(STEALTH);
		$destealthed = true;
	}
} // End of the skill use SUCCESS block.

$ending_turns = $player->changeTurns($turns_to_take); // Take the skill use cost.

$target_ending_health = $target->health();
$target_name = $target->name();

display_page(
	'skills_mod.tpl'
	, 'Skill Effect'
	, get_defined_vars()
	, array(
		'quickstat' => 'player'
	)
);
