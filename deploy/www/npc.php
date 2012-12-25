<?php
require_once(LIB_ROOT."control/lib_inventory.php");
require_once(LIB_ROOT."data/lib_npc.php");
$alive      = true;
$private    = true;

if ($error = init($private, $alive)) {
	display_error($error);
} else {

$turn_cost  = 1;
$health     = 1;
$victim     = in('victim');
$random_encounter = (rand(1, 200) == 200);
$combat_data = array();
$char_id = self_char_id();
$player     = new Player($char_id);
$error_template = 'npc.no-one.tpl'; // Error template also used down below.
$npc_template = $error_template; // Error condition by default.
$turns = $player->turns();




$npcs = get_npcs();

if($turns > 0 && !empty($victim)) {
	// Strip stealth when attacking samurai or oni
	if ($player->hasStatus('stealth') && (strtolower($victim) == 'samurai' || strtolower($victim) == 'oni')) {
		$player->subtractStatus(STEALTH);
	}

	$attacker_str    = $player->getStrength();
	$attacker_health = $player->vo->health;
	$attacker_gold   = $player->vo->gold;


	// Perform a random encounter (currently only oni).
	if ($random_encounter) { // *** ONI, Mothafucka! ***

		// **********************************************************
		// *** Oni attack! Yay!                                   ***
		// *** They take turns and a kill and do a little damage. ***
		// **********************************************************

		$victim          = 'Oni';
		$oni_turn_loss   = 10;
		$oni_health_loss = rand(1, 20);
		$oni_kill_loss   = 1;
		$player_turns    = subtractTurns($char_id, $oni_turn_loss);
		$attacker_health = $player->vo->health = subtractHealth($char_id, $oni_health_loss);
		$attacker_kills  = subtractKills($char_id, $oni_kill_loss);

		if ($attacker_health > 0) { // *** if you survive ***
			if ($player_turns > 50) { // *** And youir turns are high/you are energetic, you can kill them. ***
				$oni_killed = true;
				add_item($char_id, 'dimmak', 1);
			} else if ($player_turns > 25 && rand()&1) { // *** If your turns are somewhat high/you have some energy, 50/50 chance you can kill them. ***
				$oni_killed = true;
				add_item($char_id, 'ginsengroot', 4);
			} else {
				$oni_killed = false;
			}
		} else {
			$oni_killed = false;
		}

		$npc_template = 'npc.oni.tpl';
		$combat_data = array('victory'=>$oni_killed);
		
		
		
	} elseif (array_key_exists($victim, $npcs)){ /**** Abstracted NPCs *****/
		$npc_stats = $npcs[$victim]; // Pull an npcs individual stats with generic fallbacks.
		$display_name = first_value(@$npc_stats['name'], ucfirst($victim));


	/* ============= STANDARD NPCS ======================= */

// Initial, naive damage calculcations from npcs.
function damage_from_npc_stats($stats){
	// Formula is:  Start at zero, random integer between zero and strength * 10, add the base damage mod at the end.
	return rand(0, (1 + (@$stats['strength'] * 2))) + @$stats['damage'];
}

// Calculate npc difficulty, naively at the moment.
function npc_difficulty($stats){
	// Just add together all the points of the mob, so to speak.
	$bounty = isset($stats['bounty'])? 1 : 0;
	return 10 + @$stats['strength'] * 2 + @$stats['damage'] + $bounty;
}

		$str = whichever(@$npc_stats['strength'], 0);
		$spd = whichever(@$npc_stats['speed'], 0);
		$sta = whichever(@$npc_stats['stamina'], 0);
		$ki = whichever(@$npc_stats['ki'], 0);
		$attack_damage = damage_from_npc_stats($npc_stats);
		$percent_damage = null; // Initial percent calculation for display.
		$status_effect = whichever(@$npc_stats['status'], null);
		$reward_item = first_value(@$npc_stats['item'], null);
		$base_gold = npc_difficulty($npc_stats); // Overridden by explicitly setting gold to zero.
		$npc_gold = (int) @$npc_stats['gold'];
		$is_perceptive = @$npc_stats['speed']>11? true : false; // Beyond basic speed and they see you coming, so show that message.
		// If npc explicitly set to 0, then none will be given.
		$reward_gold = $npc_gold === 0? 0 : 
			(!$reward_item? $base_gold : round($base_gold * .9)); // Hack a little off reward gold if items received.
		$bounty_mod = @$npc_stats['bounty'];
		$image = @$npc_stats['img'];
		$image_path = null;
		if($image && file_exists(SERVER_ROOT.'www/images/characters/'.$image)){
			// If the image exists, set the path to it for use on the page.
			$image_path = IMAGE_ROOT.'characters/'.$image;
		}
		//debug($image, $image_path, SERVER_ROOT.'www/images/characters/'.$image);die();
		
		
		
		$statuses = null;
		$status_classes = null;
		
		// Assume defeat...
		$victory = null;
		$received_gold = null;
		$received_item = null;
		$reward_item_display = null;
		$added_bounty = null;
		$is_rewarded = null; // Gets items or gold.
		
		// Add bounty where applicable.
		if((bool)$bounty_mod){
			$attacker_level = $player->vo->level;

			// *** Bounty or no bounty ***
			if ($attacker_level > 5) {
				if ($attacker_level <= 50) { // No bounty after level 20?
					$added_bounty = floor($attacker_level / 3 * $bounty_mod);
					addBounty($char_id, ($added_bounty));
				}
			}	// *** End of if > 5 ***
		}
		
		// Get percent of total initial health.
		
		// ******* FIGHT *********** & Hope for victory.
		if($player->vo->health = $victory = subtractHealth($char_id, $attack_damage)) {
			// Victory occurred, reward the poor sap.
			add_gold($char_id, $reward_gold);
			$received_gold = rand(floor($reward_gold/5), $reward_gold);
			$reward_item_info = item_info_from_identity($reward_item);
			$reward_item_display = $reward_item_info['item_display_name'];
			if($reward_item_info && $reward_item_info['item_internal_name']){
				add_item($char_id, $reward_item_info['item_internal_name'], $quantity = 1);
			}
			$received_item = $reward_item;
			$is_rewarded = (bool)$received_item || (bool) $reward_gold;
			if($status_effect){
				$player->addStatus($status_effect);
			}
		}
		
		if($status_effect){
			$statuses = implode(get_status_list(), ', ');
			$status_classes = implode(get_status_list(), ' '); // TODO: Take healthy out of the list since it's redundant.
		}
		
		// Settings to display results.
		$npc_template = 'npc.abstract.tpl';
		$combat_data = array('victim'=>$victim, 'display_name'=>$display_name, 'attack_damage'=>$attack_damage, 'percent_damage'=>$percent_damage,
			'status_effect'=>$status_effect, 'statuses'=>$statuses, 'received_gold'=>$received_gold,
			'received_item'=>$reward_item_display, 'is_rewarded'=>$is_rewarded, 'victory'=>$victory, 'image_path'=>$image_path, 'npc_stats'=>$npc_stats, 'is_perceptive'=>$is_perceptive,
			'added_bounty'=>$added_bounty);
			
			
			
			
	// ******************** START of logic for specific npcs ************************
	
	} else if ($victim == 'peasant') { // *** PEASANT, was VILLAGER ***
		$villager_attack = rand(0, 10); // *** Villager Damage ***
		$just_villager = rand(0, 20);
		$added_bounty  = 0;

		if ($player->vo->health = $victory = subtractHealth($char_id, $villager_attack)) {	// *** Player defeated villager ***
			$villager_gold = rand(0, 20);	// *** Vilager Gold ***
			add_gold($char_id, $villager_gold);

			$attacker_level = $player->vo->level;

			// *** Bounty or no bounty ***
			if ($attacker_level > 1) {
				if ($attacker_level <= 20) {
					$added_bounty = floor($attacker_level / 3);
					addBounty($char_id, ($added_bounty));
				}
			}	// *** End of if > 5 ***

			if (!$just_villager) { // *** Something beyond just a villager, drop a shuriken. ***
				add_item($char_id, 'shuriken', $quantity = 1);
			}
		} else {	// *** Player lost against villager ***
			$villager_gold  =
			$attacker_level =
			$added_bounty   = 0;
		}

		$npc_template = 'npc.peasant.tpl';
		$combat_data = array('just_villager'=>$just_villager, 'attack'=>$villager_attack, 
			'gold'=>$villager_gold, 'level'=>$attacker_level, 'bounty'=>$added_bounty, 'victory'=>$victory);
	} else if ($victim == "samurai") {
		$attacker_level = $player->vo->level;
		$attacker_kills = $player->vo->kills;

		if ($attacker_level < 2 || $attacker_kills < 1) {
			$turn_cost = 0;
			$error = 'You are too weak to attack the samurai.';
		} else {
			$turn_cost = 1;

			$ninja_str               = $player->getStrength();
			$ninja_health            = $player->vo->health;
			$drop                    = false;
			$drop_display 			 = null;

			$samurai_damage_array    = array();

			$samurai_damage_array[0] = rand(1, $ninja_str);
			$samurai_damage_array[1] = rand(10, 10 + round($ninja_str * 1.2));
			$does_ninja_succeed      = rand(0, 1);

			if ($does_ninja_succeed) {
				$samurai_damage_array[2] = rand(30 + round($ninja_str * 0.2), 30 + round($ninja_str * 1.7));
			} else {
				$samurai_damage_array[2] = abs($ninja_health - $samurai_damage_array[0] - $samurai_damage_array[1]);  //Instant death.
			}

			for ($i = 0; $i < 3 && $ninja_health > 0; ++$i) {
				$ninja_health = $ninja_health - $samurai_damage_array[$i];
			}

			if ($ninja_health > 0) {	// *** Ninja still has health after all three attacks. ***
				$victory = true;

				$samurai_gold = rand(50, 50 + $samurai_damage_array[2] + $samurai_damage_array[1]);

				add_gold($char_id, $samurai_gold);
				addKills($char_id, 1);

				if ($samurai_damage_array[2] > 100) {	// *** If samurai damage was over 100, but the ninja lived, give some extra rewards. ***
					if (rand(0, 1)) {
						$drop = true;
						$drop_display = 'mushroom powder';
						add_item($char_id, 'amanita', 1);
					} else {
						$drop = true;
						$drop_display = 'a strange herb';
						add_item($char_id, 'ginsengroot', 1);
					}
				}

				if ($samurai_damage_array[2] == $ninja_str * 3) {	// *** If the final damage was the exact max damage... ***
					$drop = true;
					$drop_display = 'a black scroll';
					add_item($char_id, "dimmak", 1);
				}

				$player->vo->health = setHealth($char_id, $ninja_health);
			} else {
				$player->vo->health = setHealth($char_id, 0);
				$victory = false;
				$ninja_str    =
				$samurai_gold = 0;
			}
		}	// *** End valid turns and kills for the attack. ***

		$npc_template = 'npc.samurai.tpl';
		$combat_data = array();
		if(!$error){
			$combat_data  = array('samurai_damage_array'=>$samurai_damage_array, 'gold'=>$samurai_gold, 'victory'=>$victory, 'ninja_str'=>$ninja_str, 'level'=>$attacker_level, 'attacker_kills'=>$attacker_kills, 'drop'=>$drop, 'drop_display'=>$drop_display);
		}
	} else if ($victim == 'merchant') {
		$merchant_attack = rand(15, 35);  // *** Merchant Damage ***
		$added_bounty    = 0;

		if ($player->vo->health = $victory = subtractHealth($char_id, $merchant_attack)) {	// *** Player killed merchant ***
			$merchant_gold   = rand(20, 70);  // *** Merchant Gold   ***
			add_gold($char_id, $merchant_gold);

			if ($merchant_attack > 34) {
				add_item($char_id, 'phosphor', $quantity = 1);
			}

			if ($player->vo->level > 10) {
				$added_bounty = 5 * floor(($player->vo->level - 5) / 3);
				addBounty($char_id, $added_bounty);
			}
		} else {	// *** Merchant killed player
			$merchant_attack = $merchant_gold = 0;
		}

		$npc_template = 'npc.merchant.tpl';
		$combat_data  = array('attack'=>$merchant_attack, 'gold'=>$merchant_gold, 'bounty'=>$added_bounty, 'victory'=>$victory);
	} else if ($victim == 'guard') {	// *** The Player attacks the guard ***
		$guard_attack = rand(1, $attacker_str + 10);  // *** Guard Damage ***
		$herb         = false;
		$added_bounty = 0;

		if ($player->vo->health = $victory = subtractHealth($char_id, $guard_attack)) {
			$guard_gold = rand(1, $attacker_str + 40);	// *** Guard Gold ***
			add_gold($char_id, $guard_gold);

			if ($player->vo->level > 15) {
				$added_bounty = 10 * floor(($player->vo->level - 10) / 5);
				addBounty($char_id, $added_bounty);
			}

			if (rand(1, 9) == 9) { // *** 1/9 chance of getting an herb for Kampo ***
				$herb = true;
				add_item($char_id, 'ginsengroot', 1);
			} else {
				$herb = false;
			}
		} else {	// *** The Guard kills the player ***
			$guard_attack =
			$guard_gold   =
			$added_bounty = 0;
		}

		$npc_template = 'npc.guard.tpl';
		$combat_data  = array('attack'=>$guard_attack, 'gold'=>$guard_gold, 'bounty'=>$added_bounty, 'victory'=>$victory, 'herb'=>$herb);
	} else if ($victim == 'thief') {
		// Check the counter to see whether they've attacked a thief multiple times in a row.
		if (SESSION::is_set('counter')) {
			$counter = SESSION::get('counter');
		} else {
			$counter = 1;
		}

		$counter = $counter + 1;
		SESSION::set('counter', $counter); // Save the current state of the counter.

		if ($counter > 20 && rand(1, 3) == 3) {
			// Only after many attacks do you have the chance to be attacked back by the group of theives.
			SESSION::set('counter', 0); // Reset the counter to zero.
			$group_attack= rand(50, 150);

			if ($player->vo->health = $victory = subtractHealth($char_id, $group_attack)) {	// The den of thieves didn't accomplish their goal
				$group_gold = rand(100, 300);

				if ($group_attack > 120) { // Powerful attack gives an additional disadvantage
					subtractKills($char_id, 1);
				}

				add_gold($char_id, $group_gold);
				add_item($char_id, 'phosphor', $quantity = 1);
			} else {	// If the den of theives killed the attacker.
				$group_gold = 0;
			}

			$npc_template = 'npc.thief-group.tpl';
			$combat_data = array('attack'=>$group_attack, 'gold'=>$group_gold, 'victory'=>$victory);
		} else { // Normal attack on a single thief.
			$thief_attack = rand(0, 35);  // *** Thief Damage  ***

			if ($player->vo->health = $victory = subtractHealth($char_id, $thief_attack)) {
				$thief_gold = rand(0, 40);  // *** Thief Gold ***

				if ($thief_attack > 30) {
					subtract_gold($char_id, $thief_gold);
				} else if ($thief_attack < 30) {
					add_gold($char_id, $thief_gold);
					add_item($char_id, 'shuriken', $quantity = 1);
				}
			} else {
				$thief_gold = 0;
			}

			$npc_template = 'npc.thief.tpl';
			$combat_data = array('attack'=>$thief_attack, 'gold'=>$thief_gold, 'victory'=>$victory);
		}
	}
	
	// ************ End of specific npc logic *******************
	
	
	

	// ************ FINAL CHECK FOR DEATH ***********************
	if ($player->health() <= 0) {
		$health = false;
		sendMessage("SysMsg", $username, "DEATH: You have been killed by a ".$victim." on $today");
	}
	
	
	// Subtract the turn cost for attacking an npc, almost always going to be 1 apart from perhaps oni or group-of-thieves
	subtractTurns($char_id, $turn_cost);
}

// Add the combat_data into the standard stuff.
display_page(
	'npc.tpl'
	, 'Battle'
	, array(
		'npc_template'       => $npc_template
		, 'attacked'         => 1
		, 'turns'            => $turns
		, 'random_encounter' => $random_encounter
		, 'health'           => $health
	)+$combat_data
	, array (
		'quickstat' => 'player'
	)
);
}
?>
