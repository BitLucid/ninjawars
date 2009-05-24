<?php
require_once(substr(__FILE__,0,(strpos(__FILE__, 'webgame/')))."webgame/lib/base.inc.php");
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

include "interface/header.php";
?>

<span class="brownHeading">Battle Status</span>

<hr />

<?php
// TODO: Turn this page/system into an object to be run.

// *** ********* GET VARS FROM POST - OR GET ************* ***
$attacked = in('attacked'); // boolean for attacking again
$target = $attackee = either(in('target'), in('attackee'));
$username = get_username(); // Pulls from an internal source.
$attacker = $username;

// *** *************************************************** ***
// Target's stats.
$attackee_health = $starting_attackee_health    = getHealth($target);
$attackee_level     = getLevel($target);
$attackee_str       = getStrength($target);
$attackee_status    = getStatus($target);

// Attacker's stats.
$attacker_health    = getHealth($username);
$attacker_level     = getLevel($username);
$user_turns         = getTurns($username);
$starting_turns = $user_turns;
$killpoints			  = 0;  //Starting state for killpoints.
$attack_turns       = 1;  //Default cost, will go to zero if an error prevents combat.
$required_turns     = $attack_turns;
$level_check        = $attacker_level - $attackee_level;
$attacker_str       = getStrength($username);
$attacker_status    = getStatus($username);
$class              = getClass($username);
$what               = "";  //This will be the attack type string, e.g. "duel".
$loot                 =0;
$duel			= (in('duel')? true : NULL);
$blaze		 = (in('blaze')? true : NULL);
$deflect = (in('deflect')? true : NULL);
$simultaneousKill 	= NULL; // *** Not simultaneous by default.
$stealthAttackDamage = $attacker_str;
$turns_to_take = null;  // *** Even on failure take at least one turn.

$attack_type = 'attack'; // Default attack category type is single attack.

if($blaze){
    $attack_type = 'blaze';
} elseif ($deflect) {
    $attack_type = 'deflect';
} elseif ($duel){
    $attack_type = 'duel';
}
$skillListObj = new Skill();
$ignores_stealth = $skillListObj->getIgnoreStealth($attack_type);
$required_turns = $skillListObj->getTurnCost($attack_type);

// Attack Legal section
$attacker = $username;
$params = array('required_turns'=>$required_turns, 'ignores_stealth'=>$ignores_stealth);
assert($attacker != $target);
$AttackLegal = new AttackLegal($attacker, $target, $params);
$attack_allowed = $AttackLegal->check();
$attack_error = $AttackLegal->getError();
/*There's a same-domain problem with this attack-legal now that it's been modified for skills */



// ***  MAIN BATTLE ALGORITHM  ***
if(!$attack_allowed){ //Checks for error conditions before starting.
	echo "<div class='ninja-error centered'>$attack_error</div>"; // Display the reason the attack failed.
} else {
	// *** ATTACKING + STEALTHED SECTION  ***
	if (!$duel && $attacker_status['Stealth']){  //Not dueling, and attacking from stealth
		echo "You are striking from the shadows, you quickly strike your victim!<br />\n";
		subtractStatus($username,STEALTH);
		$turns_to_take = 1;    
		echo "Your attack has revealed you from the shadows! You are no longer stealthed.<br />\n";
		if (!subtractHealth($attackee, $stealthAttackDamage)){  // if Stealth attack of whatever damage kills target.
			echo "<div class='ninja-notice'>You have slain $attackee with a dastardly attack!</div>\n";
			echo "You do not receive recognition for this kill.<br />\n";

			$gold_mod = .1;
			subtractStatus($attackee,STEALTH+POISON+FROZEN+CLASS_STATE);
			$loot = round($gold_mod*getGold($attackee));
			addGold($username,$loot);
			subtractGold($attackee,$loot);
	     
			$attackee_msg = "DEATH: You have been killed by a stealthed ninja in combat and lost $loot gold on $today!";
			sendMessage("A Stealthed Ninja",$attackee,$attackee_msg);
	  
			$attacker_msg = "You have killed $attackee in combat and taken $loot gold on $today.";
			sendMessage($attackee,$username,$attacker_msg);

			runBountyExchange($username, $attackee);  // Determines the bounty for normal attacking.
        
			echo "<hr />\n";
		}else{  // if damage from stealth only hurts the target.
			echo "$attackee has lost ".$stealthAttackDamage." HP.<br />\n";
	  
			sendMessage($username,$attackee,"$username has attacked you from the shadows for ".$stealthAttackDamage." damage.");
		}
	}else {  // If the attacker is purely dueling or attacking, even if stealthed, though stealth is broken by dueling.
        // *** MAIN DUELING SECTION ***
        
        if ($attacker_status['Stealth']){ // *** Remove their stealth if they duel instead of preventing dueling.
            subtractStatus($username, STEALTH);
            echo "You have lost your stealth.";
        }

		if ($blaze){
			echo "Your soul blazes with fire!\n";
		}
		if ($deflect){
			echo "You center your body and soul before battle!\n";
		}
          
		// *** PRE-BATTLE STATS ***
		preBattleStats();  //Displays the starting state of the attacker and defender. 
      
		// *** BEGINNING OF MAIN BATTLE ALGORITHM ***
      
		$turns_counter         = $attack_turns;
		$total_attackee_damage = 0;
		$total_attacker_damage = 0;
		$attackee_damage       = 0;
		$attacker_damage       = 0;
      
		// *** Combat Calculations ***
		$round = 1;
		$rounds = 0;  //
		while ($turns_counter > 0 && $total_attackee_damage < $attacker_health && $total_attacker_damage < $attackee_health) {
			$turns_counter -= (!$duel? 1 : 0);  // *** SWITCH BETWEEN DUELING LOOP AND SINGLE ATTACK ***
	     
			$attackee_damage = rand (1, $attackee_str);
			$attacker_damage = rand (1, $attacker_str);
	      
			if ($blaze){  //Blaze does double damage.
				$attacker_damage = $attacker_damage*2;
			}
			else if ($deflect){
				$attackee_damage = round($attackee_damage/2);
			}
	       
			$total_attackee_damage += $attackee_damage;
			$total_attacker_damage += $attacker_damage;
			$rounds++;  //Increases the number of rounds that has occured and restarts the while loop.
		}
			if ($blaze){  //Blaze does double damage.
				echo "Your attack is more powerful due to blazing!<br />\n";
			}else if ($deflect) {
				echo "Your wounds are reduced by deflecting the attack!<br />\n";
			}


		//  *** END OF MAIN BATTLE ALGORITHM ***
		echo "Total Rounds: $rounds<br />\n";
          
		finalResults();  //Displays the final damage of the combat.
	  
		// *** RESULTING PLAYER MODIFICATION ***
      
		$gold_mod = 0.20;
		
		$turns_to_take = $required_turns;
	  
		if ($duel){
			echo "<br />You spent an extra turn dueling.<br />\n";  //Reminds of Dueling turn cost.
			$gold_mod = 0.25;
			$what     = "duel";
		}

		if ($blaze){
			echo "You spent two extra turns to blaze with power.<br />\n";  //Reminds of Blaze turn cost.
		}
		if ($deflect){
			echo "You spent two extra turns in order to deflect your enemy's blows.<br />\n"; //Deflect turn cost.
		}
      
		//  *** Let the victim know who hit them ***
		$attack_type = ($duel? 'dueled' : 'attacked');
		$email_msg   = "You have been $attack_type by $username at $today for $total_attacker_damage!";
		sendMessage($username,$attackee,$email_msg);
      
		$defenderHealthRemaining = subtractHealth($attackee, $total_attacker_damage);
		$attackerHealthRemaining = subtractHealth($username, $total_attackee_damage);
      
		if ($defenderHealthRemaining<1){  // ***  ATTACKER KILLS DEFENDER! ***
			$simultaneousKill = ($attackerHealthRemaining<1); //If both died at the same time.

			$killpoints=1;   //Changes killpoints from zero to one.

			if ($duel) {
				killpointsFromDueling();  //Changes killpoints amount by dueling equation.
				$duel_log_msg     = "$username has dueled $attackee and won $killpoints killpoints at $today.";
				sendMessage("SysMsg","SysMsg",$duel_log_msg);
				sendLogOfDuel($username, $attackee, 1, $killpoints);  //Makes a WIN record in the dueling log.
			}

			addKills($username,$killpoints);  //  Attacker gains their killpoints.
			subtractStatus($attackee, STEALTH+POISON+FROZEN+CLASS_STATE);
	     
			if (!$simultaneousKill)	{
				$loot = round($gold_mod*getGold($attackee));
				addGold($username, $loot);
				subtractGold($attackee, $loot);
			}
	     
			$attackee_msg = "DEATH: You have been killed by $username in combat and lost $loot gold on $today!";
			sendMessage($username,$attackee,$attackee_msg);
	  
			$attacker_msg = "You have killed $attackee in combat and taken $loot gold on $today.";
			sendMessage($attackee,$username,$attacker_msg);
	  
			echo "$username has killed $attackee!<br />\n";
			echo "<div class='ninja-notice'>
				$attackee is dead, you have proven your might";

			if ($killpoints==2){
				echo " twice over";
			}
			elseif ($killpoints>2){
				echo " $killpoints times over";
			}

			echo "!</div>\n";
			if (!$simultaneousKill){
				echo "You have taken $loot gold from $attackee.<br />\n";
			}
	  
			runBountyExchange($username, $attackee);  //Determines bounty for dueling.
		}
	  
		if ($attackerHealthRemaining<1){  // *** DEFENDER KILLS ATTACKER!  ***
			$defenderKillpoints=1;
			if ($duel){  // if they were dueling when they died
				$duel_log_msg     = "$username has dueled $attackee and lost at $today.";
				sendMessage("SysMsg","SysMsg",$duel_log_msg);
				sendLogOfDuel($username,$attackee,0,$killpoints);  //Makes a loss in the duel log.
			}
			addKills($attackee,$defenderKillpoints);  //Adds a kill for the defender.
			subtractStatus($username,STEALTH+POISON+FROZEN+CLASS_STATE);
	      
			if (!$simultaneousKill){
				$loot = round($gold_mod*getGold($username));  //Loot for defender if he lives.
				addGold($attackee,$loot);
				subtractGold($username,$loot);
			}
	     
			$attackee_msg = "<div class='ninja-notice'>
				You have killed $username in combat and taken $loot gold on $today.
				</div>";
			sendMessage($username,$attackee,$attackee_msg);
	     
			$attacker_msg = "DEATH: You have been killed by $attackee in combat and lost $loot gold on $today!";
			sendMessage($attackee,$username,$attacker_msg);
 	      
			echo "<div class='ninja-error'>$attackee has killed you!</div>\n";
			echo "<div class='ninja-notice'>
				You have been slain!  Go to the <a href=\"shrine.php\">Shrine</a> to return to the living.<br />
				</div>\n";
			if (!$simultaneousKill) {
				echo "$attackee has taken $loot gold from you.<br />\n";
			}
		}

		// *** END MAIN ATTACK AND DUELING SECTION ***
	}

	if (!$duel && getHealth($username) > 0 && getHealth($attackee) > 0){ //After any partial attack.
		echo "<a href=\"attack_mod.php?attacked=1&attackee=$attackee\">Attack Again?</a><br />\n";
	}
}

// *** Take away at least one turn even on attacks that fail. ***
if ($turns_to_take<1) {
	$turns_to_take = 1;
}
$ending_turns = subtractTurns($username, $turns_to_take);
assert($ending_turns<$starting_turns || $starting_turns == 0);
	


//  ***  START ACTION OVER AGAIN SECTION ***
echo "<hr />\n";
if (isset($attackee)){
	echo "Return to <a href=\"player.php?player=".urlencode($attackee)."\">Player Detail</a><br />Or \n";
}
echo "Start your combat <a href=\"list_all_players.php\"> from the player list.</a>\n<br />\n";
echo "<hr /><br />\n";

include "interface/footer.php";


?>
