<?php
/*
 * Deals with the skill based attacks, and status effects.
 *
 * @package combat
 * @subpackage skill
 */
$private    = true;
$alive      = true;
$quickstat  = "player";
$page_title = "Using Skills";

include SERVER_ROOT."interface/header.php";
include(OBJ_ROOT."Skill.php");
?>

<h1>Skills</h1>

<?php
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

// Check whether the user actually has the needed skill.
$has_skill = $skillListObj->hasSkill($command);

$starting_turn_cost = $turn_cost;
assert($turn_cost>=0);
$turns_to_take = null;  // *** Even on failure take at least one turn.
$char_id = get_char_id();

$player          = new Player($char_id);

if ($target != '' && $target != $player->player_id) {
	$target = new Player($target);
	$target_id = $target->id();
	$link_back = "<a href='player.php?player_id=$target_id'>Ninja Detail</a>";
} else {
    $link_back = "<a href=\"skills.php\">Skills</a>";
	$target    = $player;
}

$user_ip         = get_account_ip();
$class           = $player->vo->class;
$covert          = false;
$victim_alive    = true;
$attacker_id     = $username;
$starting_turns  = $player->vo->turns;
$ending_turns    = null;

$level_check  = $player->vo->level - $target->vo->level;

if ($player->hasStatus(STEALTH)) {
	$attacker_id = "A Stealthed Ninja";
}

// TODO: Make attackLegal use self_use param.
// TODO: Make attackLegal also check that the skill can be used on an outside target.
// *** Checks the skill use legality, as long as the target isn't self.
$params         = array('required_turns'=>$turn_cost, 'ignores_stealth'=>$ignores_stealth, 'self_use'=>$self_use);
$AttackLegal    = new AttackLegal($player->player_id, $target->player_id, $params);
$attack_allowed = $AttackLegal->check();
$attack_error   = $AttackLegal->getError();

if ($attack_error) { // Use AttackLegal if not attacking self.
	echo "<div class='ninja-notice'>$attack_error</div>"; // Display the reason for the attack failure.
} elseif (!$has_skill || $class == "" || $command == "") {
	echo "You do not have the requested skill.\n";
} else {
	// Initial attack conditions are alright.
    echo "<div class='usage-mod-result'>";
	$result = "";

	if ($command == "Sight") {
		$covert = true;

		if ($starting_turns >= $turn_cost) {
			//$msg = "You have had sight cast on you by $attacker_id at $today";
			//sendMessage($attacker_id, $target->vo->uname, $msg);
			//$target->subtractStatus(STEALTH);
			// Sight will no longer break stealth.

			$statement = DatabaseConnection::$pdo->prepare("SELECT uname, class_name AS class, health, strength, gold, kills, turns, level FROM players JOIN class ON _class_id = class_id WHERE player_id = :player");
			$statement->bindValue(':player', $target->player_id);
			$statement->execute();

			$data = $statement->fetch(PDO::FETCH_ASSOC);

			echo "<table>\n";
			echo "<tr>\n";
			echo "  <th>Name</th>\n";
			echo "  <th>Class</th>\n";
			echo "  <th>Health</th>\n";
			echo "  <th>Str</th>\n";
			echo "  <th>Gold</th>\n";
			echo "  <th>Kills</th>\n";
			echo "  <th>Turns</th>\n";
			echo "  <th>Level</th>\n";
			echo "</tr>\n";
			echo "<tr>\n";

			foreach ($data AS $loopPart) {
				echo "<td>".$loopPart."</td>\n";
			}

			echo "</tr>";
			echo "</table>";
		} else {
			$turn_cost = 0;
			echo "You do not have enough turns to cast $command.\n";
			// Ye gods this repeated code needs to be made into a single check.
		}
	} elseif  ($command == "Steal") {
		$covert = true;

		if ($starting_turns >= $turn_cost) {
			$gold_decrease = rand(1, 50);
			$target_gold   = $target->vo->gold;
			$gold_decrease = ($target_gold < $gold_decrease ? $target_gold : $gold_decrease);

			changeGold($username, $gold_decrease); // *** This one actually adds the value.
			subtractGold($target->vo->uname, $gold_decrease); // *** Subtracts whatever positive value is put in.

			$msg = "You have had pick pocket cast on you for $gold_decrease by $attacker_id at $today";
			sendMessage($attacker_id, $target->vo->uname, $msg);

			$result = "You have stolen $gold_decrease gold from $target!<br>\n";
		} else {
			$turn_cost = 0;
			echo "You do not have enough turns to cast $command.\n";
		}
	} else if ($command == 'Unstealth') {
		$state = 'unstealthed';

		if ($starting_turns >= $turn_cost) {
			if ($target->hasStatus(STEALTH)) {
				$target->subtractStatus(STEALTH);
				echo "You are now $state.<br>\n";
			} else {
				$turn_cost = 0;
				echo "$target is already $state.\n";
			}
		} else {
			$turn_cost = 0;
			echo "You do not have enough turns to cast $command.\n";
		}
	} else if ($command == 'Stealth') {
		$covert     = true;
		$state      = 'stealthed';

		if ($starting_turns >= $turn_cost) {
			if (!$target->hasStatus(STEALTH)) {
				$target->addStatus(STEALTH);
				echo "You are now $state.<br>\n";
			} else {
				$turn_cost = 0;
				echo "$target is already $state.\n";
			}
		} else {
			$turn_cost = 0;
			echo "You do not have enough turns to cast $command.\n";
		}
	} else if ($command == "Kampo") {
		$covert = true;

		if ($starting_turns >= $turn_cost) {
			// *** Get Special Items From Inventory ***
			$user_id = get_user_id();
			DatabaseConnection::getInstance();
			$statement = DatabaseConnection::$pdo->prepare("SELECT sum(amount) AS c FROM inventory WHERE owner = :owner AND item = 'Ginseng Root' GROUP BY item");
			$statement->bindValue(':owner', $user_id);
			$statement->execute();

			if ($itemCount = $statement->fetchColumn()) {	// *** If special item count > 0 ***
				$itemsConverted = min($itemCount, $starting_turns);
				removeItem($user_id, 'Ginseng Root', $itemsConverted);
				addItem($username, 'Tiger Salve', $itemsConverted);
				$turn_cost = $itemsConverted;
				echo "With intense focus you grind the herbs into potent formulas.\n";
			} else { // *** no special items, give error message ***
				$turn_cost = 0;
				echo "You do not have the necessary ingredients for any Kampo formulas.\n";
			}
		} else {
			$turn_cost = 0;
			echo "You do not have enough turns to use $command.\n";
		}
	} else if ($command == 'Poison Touch') {
		$covert = true;

		if ($starting_turns >= $turn_cost) {
			$target->addStatus(POISON);

			$target_damage = rand($poisonMinimum, $poisonMaximum);

			$victim_alive = subtractHealth($target->vo->uname, $target_damage);
			echo "$target has beeen poisoned!<br>\n";
			echo "$target has taken $target_damage damage!<br>\n";

			$msg = "You have been poisoned by $attacker_id at $today";
			sendMessage($attacker_id, $target->vo->uname, $msg);
		} else {
			$turn_cost = 0;
			echo "You do not have enough turns to cast $command.\n";
		}
	} elseif ($command == 'Fire Bolt') {
		if ($starting_turns >= $turn_cost) {
			$target_damage = (5 * (ceil($player->vo->level / 3)) + rand(1, $player->getStrength()));

			echo "$target has taken $target_damage damage!<br>\n";

			if ($victim_alive = subtractHealth($target->vo->uname, $target_damage)) {
				$attacker_id  = $username;
			}

			$msg = "You have had fire bolt cast on you by $attacker_id at $today";
			sendMessage($attacker_id, $target->vo->uname, $msg);
		} else {
			$turn_cost = 0;
			echo "You do not have enough turns to cast $command.\n";
		}
	} else if ($command == 'Heal') {
		if ($starting_turns >= $turn_cost) {
		    // Check that the target is not already status healing.
		    $heal_per_level = 10;
		    $healed_by = $player->level()*$heal_per_level;		    
		    $target_current_health = $target->health();
		    $target_max_health = $target->max_health();
		    if($target->hasStatus(HEALING)){
		        $turn_cost = 0;
		        echo $target->name()." is already under a healing aura.";
            } elseif($target_current_health>=$target_max_health){
                $turn_cost = 0;
                echo $target->name()." is already fully healed.";
            } else {
    		    $new_health = $target->heal($healed_by);
    		    $target->addStatus(HEALING);
    		    $result = $target->name()." healed by $healed_by to $new_health.<br>";
    		    if($target->name() != $player->name()){
        		    sendMessage($attacker_id, $target->name(), 
        		        "You have been healed by $attacker_id at $today for $healed_by.");
        		}
            }
		} else {
			$turn_cost = 0;
			echo "You do not have enough turns to cast $command.\n";
		}
	} else if ($command == 'Ice Bolt') {
		if ($starting_turns >= $turn_cost) {
    		if ($target->vo->turns >= 10) {
    			$turns_decrease = rand(1, 5);
    			subtractTurns($target->vo->uname, $turns_decrease);
    			// Changed ice bolt to kill stealth.
    			$target->subtractStatus(STEALTH);

    			$msg = "Ice bolt cast on you by $attacker_id at $today, your turns have been reduced by $turns_decrease.";
    			sendMessage($attacker_id, $target->vo->uname, $msg);

    			$result = "$target's turns reduced by $turns_decrease!<br>\n";
    		} else {
    		    $turn_cost = 0;
    		    $result = "$target does not have enough turns for you to take.";
    		}
		} else {
			$turn_cost = 0;
			echo "You do not have enough turns to cast $command.\n";
		}
	} else if ($command == 'Cold Steal') {
		if ($starting_turns >= $turn_cost) {
			$critical_failure = rand(1, 100);

			if ($critical_failure > 7) {// *** If the critical failure rate wasn't hit.
				if ($target->vo->turns >= 10) {
					$turns_decrease = rand(2, 7);

					subtractTurns($target->vo->uname, $turns_decrease);
					addTurns($username, $turns_decrease);

					$msg = "You have had Cold Steal cast on you for $turns_decrease by $attacker_id at $today";
					sendMessage($attacker_id, $target->vo->uname, $msg);

					$result = "You cast Cold Steal on $target and take $turns_decrease of his turns.<br>\n";
				} else {
					$turn_cost = 0;
					$result = "The victim did not have enough turns to give you.<br>\n";
				}
			} else { // *** CRITICAL FAILURE !!
				$player->addStatus(FROZEN);

				$unfreeze_time = date("F j, Y, g:i a", mktime(date("G")+1, 0, 0, date("m"), date("d"), date("Y")));

				$failure_msg = "You have experienced a critical failure while using Cold Steal on $today. You will be unfrozen on $unfreeze_time";
				sendMessage("SysMsg", $username, $failure_msg);
				$result = "Cold Steal has backfired! You have lost 3 turns and are now frozen until $unfreeze_time!<br>\n";
			}
	    } else {
			$turn_cost = 0;
			echo "You do not have enough turns to cast $command.\n";
		}
	}

	echo $result;

	if (!$victim_alive) {
		if ($target->player_id != $player->player_id) {
			$gold_mod = 0.15;
			$loot     = round($gold_mod * getGold($target->vo->uname));

			subtractGold($target->vo->uname, $loot);
			addGold($username, $loot);

			addKills($username, 1);

			echo "You have killed $target with $command!<br>\n";
			echo "You receive $loot gold from $target.<br>\n";

			$added_bounty = floor($level_check / 5);

			if ($added_bounty > 0) {
				addBounty($username, ($added_bounty * 25));
				echo "Your victim was much weaker than you. The townsfolk are angered. A bounty of ".($added_bounty * 25)." gold has been placed on your head!<br>\n";
			} else {
				if ($bounty = rewardBounty($username, $target->vo->uname)) {
					echo "You have received the $bounty gold bounty on $target's head for your deeds!<br>\n";

					$bounty_msg = "You have valiantly slain the wanted criminal, $target! For your efforts, you have been awarded $bounty gold!";
					sendMessage("Village Doshin", $username, $bounty_msg);
				}
			}

			$target_message = "$attacker_id has killed you with $command on $today and taken $loot gold.";
			sendMessage($attacker_id, $target->vo->uname, $target_message);

			$attacker_message = "You have killed $target with $command on $today and taken $loot gold.";
			sendMessage($target->vo->uname, $username, $attacker_message);
		} else {
			$loot = 0;
			echo "You have comitted suicide!<br>\n";
		}
	}

	$turns_to_take = $turns_to_take - $turn_cost;

	if (!$covert && $player->hasStatus(STEALTH)) {
		$player->subtractStatus(STEALTH);
		echo "Your actions have revealed you. You are no longer stealthed.<br>\n";
	}
} // End of the skill use SUCCESS block.

$ending_turns = changeTurns($username, $turns_to_take);
?>
  </div>
  <div class="skillReload">
    <a href="skills_mod.php?command=<?php echo urlencode($command); ?>&amp;target=<?php echo $target->player_id; ?>">Use <?php echo $command; ?> again.</a>
  </div>
  <br>
  <div class="LinkBack">
    Return to <?php echo $link_back; ?>
  </div>
</p>

<?php
include SERVER_ROOT."interface/footer.php";
?>
