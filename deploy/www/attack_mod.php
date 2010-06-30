<?php
require_once(DB_ROOT . "PlayerVO.class.php");
require_once(DB_ROOT . "PlayerDAO.class.php");
require_once(COMBAT_ROOT . "lib_combat_tests.php");
require_once(CHAR_ROOT . "Player.class.php");
require_once(LIB_ROOT."common/lib_attack.php");
require_once(OBJ_ROOT."Skill.php");
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

include SERVER_ROOT."interface/header.php";

$recent_attack = null;
$start_of_attack = microtime(true);
$attack_spacing = 0.2; // fraction of a second
if (SESSION::is_set('recent_attack')) {
    $recent_attack = SESSION::get('recent_attack');
}

if ($recent_attack && ($recent_attack > ($start_of_attack - $attack_spacing))) {
    echo "<p>Even the best of ninjas cannot attack that quickly.</p>";
    echo "<a href='attack_player.php'>Return to combat</a>";
    SESSION::set('recent_attack', $start_of_attack);
    die();
} else {
    SESSION::set('recent_attack', $start_of_attack);
}
?>

<h1>Battle Status</h1>

<hr>

<?php
// TODO: Turn this page/system into a function to be rendered.

// *** ********* GET VARS FROM POST - OR GET ************* ***
$attacked    = in('attacked'); // boolean for attacking again
$target      = either(in('target'), in('attackee'));
$duel        = (in('duel')    ? true : NULL);
$blaze       = (in('blaze')   ? true : NULL);
$deflect     = (in('deflect') ? true : NULL);
$evade       = (in('evasion') ? true : NULL);

$attacker    = get_username(); // Pulls from an internal source.
$attacker_id = get_user_id();

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

foreach ($attack_type as $type)
{
	$ignores_stealth = $ignores_stealth||$skillListObj->getIgnoreStealth($type);
	$required_turns += $skillListObj->getTurnCost($type);
}

// *** Attack Legal section ***
$params = array(
	'required_turns'    => $required_turns
	, 'ignores_stealth' => $ignores_stealth
);

assert($attacker != $target);

$AttackLegal = new AttackLegal($attacker, $target, $params);

// *** There's a same-domain problem with this attack-legal now that it's been modified for skills ***

// ***  MAIN BATTLE ALGORITHM  ***
if (!$AttackLegal->check())	{	// *** Checks for error conditions before starting.
	// *** Display the reason the attack failed.
	echo "<div class='ninja-error centered'>".$AttackLegal->getError()."</div>";
} else {
	$target_player    = new Player($target);
	$attacking_player = new Player($attacker);

	// *** Target's stats. ***
	$target_health    = $target_player->vo->health;
	$target_level     = $target_player->vo->level;
	$target_str       = $target_player->getStrength();
	$target_status    = $target_player->getStatus();

	// *** Attacker's stats. ***
	$attacker_health     = $attacking_player->vo->health;
	$attacker_level      = $attacking_player->vo->level;
	$attacker_turns      = $attacking_player->vo->turns;
	$attacker_str        = $attacking_player->getStrength();
	$attacker_status     = $attacking_player->getStatus();
	$class               = $attacking_player->vo->class;

	$starting_target_health   = $target_health;
	$starting_turns           = $attacker_turns;
	$stealthAttackDamage      = $attacker_str;
	$level_check              = $attacker_level - $target_level;

	$loot   = 0;
	$victor = null;
	$loser  = null;

	// *** ATTACKING + STEALTHED SECTION  ***
	if (!$duel && $attacker_status['Stealth']) { // *** Not dueling, and attacking from stealth ***
		$attacking_player->subtractStatus(STEALTH);
		$turns_to_take = 1;

		echo "<div>You are striking from the shadows, you quickly strike your victim!</div>\n";
		echo "<div>Your attack has revealed you from the shadows! You are no longer stealthed.</div>\n";

		if (!subtractHealth($target, $stealthAttackDamage)) { // *** if Stealth attack of whatever damage kills target. ***
			$victor = $attacker;
			$loser  = $target;

			$gold_mod     = .1;
			$loot         = round($gold_mod * getGold($target));

			$target_msg   = "DEATH: You have been killed by a stealthed ninja in combat and lost $loot gold on $today!";
			$attacker_msg = "You have killed $target in combat and taken $loot gold on $today.";

			$target_player->death();
			sendMessage("A Stealthed Ninja", $target, $target_msg);
			sendMessage($target, $attacker, $attacker_msg);
			runBountyExchange($attacker, $target); // *** Determines the bounty for normal attacking. ***

			echo "<div>You have slain $target with a dastardly attack!\n";
			echo "You do not receive recognition for this kill.</div>\n";
			echo "<hr>\n";
		} else {	// *** if damage from stealth only hurts the target. ***
			echo "<div>$target has lost ".$stealthAttackDamage." HP.</div>\n";

			sendMessage($attacker, $target, "$attacker has attacked you from the shadows for $stealthAttackDamage damage.");
		}
	} else {	// *** If the attacker is purely dueling or attacking, even if stealthed, though stealth is broken by dueling. ***
       // *** MAIN DUELING SECTION ***

        if ($attacker_status['Stealth']) { // *** Remove their stealth if they duel instead of preventing dueling.
            $attacking_player->subtractStatus(STEALTH);
            echo "You have lost your stealth.";
        }

		if ($blaze) {
			echo "Your soul blazes with fire!\n";
		}

		if ($deflect) {
			echo "You center your body and soul before battle!\n";
		}

		if ($evade) {
			echo "As you enter battle, you note your potential escape routes...\n";
		}

		// *** PRE-BATTLE STATS ***
		preBattleStats($target_player, $attacking_player);	// *** Displays the starting state of the attacker and defender. ***

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
				$testValue = ($attacker_health - $total_target_damage);

				if ($testValue < ($target_str*.5) || $testValue < ($attacker_health*.1)) {
					break;
				}
			}
		}

		if ($blaze) {	// *** Blaze does double damage. ***
			echo "<div>Your attack is more powerful due to blazing!</div>\n";
		}

		if ($deflect) {
			echo "<div>Your wounds are reduced by deflecting the attack!</div>\n";
		}

		if ($evade && $total_attacker_damage < $target_health) {
			echo "<div>Realizing you are out matched, you escape with your life to fight another day!</div>";
		}

		// *** END OF MAIN BATTLE ALGORITHM ***
		echo "<div>Total Rounds: $rounds</div>\n";

		finalResults();	// *** Displays the final damage of the combat. ***

		// *** RESULTING PLAYER MODIFICATION ***

		$gold_mod = 0.20;

		$turns_to_take = $required_turns;

		if ($duel) {
			echo "<br>You spent an extra turn dueling.<br>\n";	// *** Reminds of Dueling turn cost. ***
			$gold_mod = 0.25;
			$what     = "duel";
		}

		if ($blaze) {
			echo "<div>You spent two extra turns to blaze with power.</div>\n";	// *** Reminds of Blaze turn cost. ***
		}

		if ($deflect) {
			echo "<div>You spent three extra turns in order to deflect your enemy's blows.</div>\n"; // *** Deflect turn cost. ***
		}

		if ($evade) {
			echo "<div>You spent 2 extra turns preparing your escape routes.</div>\n"; // *** Evade turn cost. ***
		}

		//  *** Let the victim know who hit them ***
		$attack_label = ($duel ? 'dueled' : 'attacked');

		$defenderHealthRemaining = subtractHealth($target, $total_attacker_damage);
		$attackerHealthRemaining = subtractHealth($attacker, $total_target_damage);

		if ($defenderHealthRemaining && $attackerHealthRemaining) {
			$email_msg = "You have been $attack_label by $attacker at $today for $total_attacker_damage, but they got away before you could kill them!";
		} else {
			$email_msg = "You have been $attack_label by $attacker at $today for $total_attacker_damage!";
		}

		sendMessage($attacker, $target, $email_msg);

		if ($defenderHealthRemaining < 1 || $attackerHealthRemaining < 1) {
			if ($defenderHealthRemaining < 1) { //***  ATTACKER KILLS DEFENDER! ***
				if ($simultaneousKill = ($attackerHealthRemaining < 1)) { // *** If both died at the same time. ***
				} else {
					$victor = $attacker;
					$loser  = $target;
				}

				$killpoints = 1; // *** Changes killpoints from zero to one. ***

				if ($duel) {
					killpointsFromDueling();	// *** Changes killpoints amount by dueling equation. ***
					$duel_log_msg     = "$attacker has dueled $target and won $killpoints killpoints at $today.";
					sendMessage("SysMsg", "SysMsg", $duel_log_msg);
					sendLogOfDuel($attacker, $target, 1, $killpoints);	// *** Makes a WIN record in the dueling log. ***
				}

				addKills($attacker, $killpoints); // *** Attacker gains their killpoints. ***
				$target_player->death();

				if (!$simultaneousKill)	{
					$loot = round($gold_mod * getGold($target));
				}

				$target_msg = "DEATH: You have been killed by $attacker in combat and lost $loot gold on $today!";
				sendMessage($attacker, $target, $target_msg);

				$attacker_msg = "You have killed $target in combat and taken $loot gold on $today.";
				sendMessage($target, $attacker, $attacker_msg);

				echo "<div>$attacker has killed $target!</div>\n";
				echo "<div class='ninja-notice'>
					$target is dead, you have proven your might";

				if ($killpoints == 2) {
					echo " twice over";
				} elseif ($killpoints > 2) {
					echo " $killpoints times over";
				}

				echo "!</div>\n";

				if (!$simultaneousKill) {
					echo "<div>You have taken $loot gold from $target.</div>\n";
				}

				runBountyExchange($attacker, $target);	// *** Determines bounty for dueling. ***
			}

			if ($attackerHealthRemaining < 1) { // *** DEFENDER KILLS ATTACKER! ***
				if ($simultaneousKill = ($attackerHealthRemaining < 1)) { // *** If both died at the same time. ***
				} else {
					$victor = $target;
					$loser  = $attacker;
				}
				$defenderKillpoints = 1;

				if ($duel) {	// *** if they were dueling when they died ***
					$duel_log_msg     = "$attacker has dueled $target and lost at $today.";
					sendMessage("SysMsg", "SysMsg", $duel_log_msg);
					sendLogOfDuel($attacker, $target, 0, $killpoints);	// *** Makes a loss in the duel log. ***
				}

				addKills($target, $defenderKillpoints);	// *** Adds a kill for the defender. ***
				$attacking_player->death();

				if (!$simultaneousKill) {
					$loot = round($gold_mod * getGold($attacker));//Loot for defender if he lives.
				}

				$target_msg = "You have killed $attacker in combat and taken $loot gold on $today.";

				$attacker_msg = "DEATH: You have been killed by $target in combat and lost $loot gold on $today!";

				sendMessage($attacker, $target, $target_msg);
				sendMessage($target, $attacker, $attacker_msg);

				echo "<div class='ninja-error'>$target has killed you!</div>\n";
				echo "<div class='ninja-notice' style='margin-bottom: 10px;'>
					Go to the <a href=\"shrine.php\">Shrine</a> to return to the living.
					</div>\n";

				if (!$simultaneousKill) {
					echo "<div>$target has taken $loot gold from you.</div>\n";
				}
			}
		}

		// *** END MAIN ATTACK AND DUELING SECTION ***
	}

	if ($loot) {
		addGold($victor, $loot);
		subtractGold($loser, $loot);
	}
}

// *** Take away at least one turn even on attacks that fail. ***
if ($turns_to_take < 1) {
	$turns_to_take = 1;
}

$ending_turns = subtractTurns($attacker, $turns_to_take);

//  ***  START ACTION OVER AGAIN SECTION ***
echo "<hr>\n";

if (isset($target)) {
	if ($AttackLegal && getHealth($attacker) > 0 && getHealth($target) > 0) {	// *** After any partial attack. ***
		echo "<div><a href=\"attack_mod.php?attacked=1&amp;target=$target\">Attack Again?</a></div>\n";
	}

	echo "<div>Return to <a href=\"player.php?player=".urlencode($target)."\">".out($target)."'s Info</a></div>Or \n";
}

echo "Start your combat <a href=\"list_all_players.php\"> from the player list.</a>\n<br>\n";
echo "<hr><br>\n";

include(SERVER_ROOT."interface/footer.php");
?>
