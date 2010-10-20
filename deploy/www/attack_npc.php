<?php
$alive      = true;
$private    = true;

if ($error = init($private, $alive)) {
	display_error($error);
} else {

$turn_cost  = 1;
$health     = 1;
$attacked   = in('attacked');
$victim     = in('victim');
$random_encounter = (rand(1, 200) == 200);
$combat_data = array();
$char_id = get_char_id();
$player     = new Player($char_id);

if (($turns = getTurns($username)) > 0) {
	if ($attacked == 1) { // *** Bit to expect that it comes from the form. ***
		if ($player->hasStatus(STEALTH)) {
			$player->subtractStatus(STEALTH);
		}

		$attacker_str    = $player->getStrength();
		$attacker_health = $player->vo->health;
		$attacker_gold   = $player->vo->gold;

		if ($random_encounter) { // *** ONI, Mothafucka! ***

			// **********************************************************
			// *** Oni attack! Yay!                                   ***
			// *** They take turns and a kill and do a little damage. ***
			// **********************************************************

			$victim          = 'Oni';
			$oni_turn_loss   = 10;
			$oni_health_loss = rand(1, 20);
			$oni_kill_loss   = 1;
			$player_turns    = subtractTurns($username, $oni_turn_loss);
			$attacker_health = $player->vo->health = subtractHealth($username, $oni_health_loss);
			$attacker_kills  = subtractKills($username, $oni_kill_loss);

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

			$npc_template = 'oni_result.tpl';
			$combat_data = array('victory'=>$oni_killed);
		} else if (empty($victim)) {
			$npc_template = 'no_npc_result.tpl';
		} else if ($victim == 'villager') { // *** VILLAGER ***
			$villager_attack = rand(0, 10); // *** Villager Damage ***
			$just_villager = rand(0, 20);
			$added_bounty  = 0;

			if ($player->vo->health = $victory = subtractHealth($username, $villager_attack)) {	// *** Player defeated villager ***
				$villager_gold = rand(0, 20);	// *** Vilager Gold ***
				addGold($username, $villager_gold);

				$attacker_level = $player->vo->level;

				// *** Bounty or no bounty ***
				if ($attacker_level > 5) {
					if ($attacker_level <= 20) {
						$added_bounty = floor($attacker_level / 3);
						addBounty($username, ($added_bounty));
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

			$npc_template = 'villager_result.tpl';
			$combat_data = array('just_villager'=>$just_villager, 'attack'=>$villager_attack, 'gold'=>$villager_gold, 'level'=>$attacker_level, 'bounty'=>$added_bounty, 'victory'=>$victory);
		} else if ($victim == "samurai") {
			$attacker_level = $player->vo->level;
			$attacker_kills = $player->vo->kills;

			if ($attacker_level < 6 || $attacker_kills < 1) {
				$turn_cost = 0;
			} else {
				$turn_cost = 1;

				$ninja_str               = $player->getStrength();
				$ninja_health            = $player->vo->health;
				$drop                    = false;

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

					addGold($username, $samurai_gold);
					addKills($username, 1);

					if ($samurai_damage_array[2] > 100) {	// *** If samurai damage was over 100, but the ninja lived, give a speed scroll. ***
						if (rand()&1) {
							$drop = 'speed';
							add_item($char_id, 'amanita', 1);
						} else {
							$drop = 'herb';
							add_item($char_id, 'ginsengroot', 1);
						}
					}

					if ($samurai_damage_array[2] == $ninja_str * 3) {	// *** If the final damage was the exact max damage... ***
						add_item($char_id, "dimmak", 1);
					}

					$player->vo->health = setHealth($username, $ninja_health);
				} else {
					$player->vo->health = setHealth($username, 0);
					$victory = false;
					$ninja_str    =
					$samurai_gold = 0;
				}
			}	// *** End valid turns and kills for the attack. ***

			$npc_template = 'samurai_result.tpl';
			$combat_data  = array('samurai_damage_array'=>$samurai_damage_array, 'gold'=>$samurai_gold, 'victory'=>$victory, 'ninja_str'=>$ninja_str, 'level'=>$attacker_level, 'attacker_kills'=>$attacker_kills, 'drop'=>$drop);
		} else if ($victim == 'merchant') {
			$merchant_attack = rand(15, 35);  // *** Merchant Damage ***
			$added_bounty    = 0;

			if ($player->vo->health = $victory = subtractHealth($username, $merchant_attack)) {	// *** Player killed merchant ***
				$merchant_gold   = rand(20, 70);  // *** Merchant Gold   ***
				addGold($username, $merchant_gold);

				if ($merchant_attack > 34) {
					add_item($char_id, 'phosphor', $quantity = 1);
				}

				if ($player->vo->level > 10) {
					$added_bounty = 5 * floor(($player->vo->level - 5) / 3);
					addBounty($username, $added_bounty);
				}
			} else {	// *** Merchant killed player
				$merchant_attack = $merchant_gold = 0;
			}

			$npc_template = 'merchant_result.tpl';
			$combat_data  = array('attack'=>$merchant_attack, 'gold'=>$merchant_gold, 'bounty'=>$added_bounty, 'victory'=>$victory);
		} else if ($victim == 'guard') {	// *** The Player attacks the guard ***
			$guard_attack = rand(1, $attacker_str + 10);  // *** Guard Damage ***
			$herb         = false;
			$added_bounty = 0;

			if ($player->vo->health = $victory = subtractHealth($username, $guard_attack)) {
				$guard_gold = rand(1, $attacker_str + 40);	// *** Guard Gold ***
				addGold($username, $guard_gold);

				if ($player->vo->level > 15) {
					$added_bounty = 10 * floor(($player->vo->level - 10) / 5);
					addBounty($username, ($added_bounty));
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

			$npc_template = 'guard_result.tpl';
			$combat_data  = array('attack'=>$guard_attack, 'gold'=>$guard_gold, 'bounty'=>$added_bounty, 'victory'=>$victory, 'herb'=>$herb);
		} else if ($victim == "thief") {
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

				if ($player->vo->health = $victory = subtractHealth($username, $group_attack)) {	// The den of thieves didn't accomplish their goal
					$group_gold = rand(100, 300);

					if ($group_attack > 120) { // Powerful attack gives an additional disadvantage
						subtractKills($username, 1);
					}

					addGold($username, $group_gold);
					add_item($char_id, 'phosphor', $quantity = 1);
				} else {	// If the den of theives killed the attacker.
					$group_gold = 0;
				}

				$npc_template = 'thief-group_result.tpl';
				$combat_data = array('attack'=>$group_attack, 'gold'=>$group_gold, 'victory'=>$victory);
			} else { // Normal attack on a single thief.
				$thief_attack = rand(0, 35);  // *** Thief Damage  ***

				if ($player->vo->health = $victory = subtractHealth($username, $thief_attack)) {
					$thief_gold = rand(0, 40);  // *** Thief Gold ***

					if ($thief_attack > 30) {
						subtractGold($username, $thief_gold);
					} else if ($thief_attack < 30) {
						addGold($username, $thief_gold);
						add_item($char_id, 'shuriken', $quantity = 1);
					}
				} else {
					$thief_gold = 0;
				}

				$npc_template = 'thief_result.tpl';
				$combat_data = array('attack'=>$thief_attack, 'gold'=>$thief_gold, 'victory'=>$victory);
			}
		}

		if ($player->vo->health <= 0) {
			$health = false;
			sendMessage("SysMsg", $username, "DEATH: You have been killed by a non-player character at $today");
		}

		subtractTurns($username, $turn_cost);
	}
} else {
	$npc_template = null;
}

display_page(
	'attack_npc.tpl'
	, 'NPC Battle Status'
	, array(
		'npc_template'       => $npc_template
		, 'attacked'         => $attacked
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
