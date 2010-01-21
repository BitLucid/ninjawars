<?php
/**
 * Attack function library.
 * @package combat
 * @subpackage lib_attack
**/

function update_last_attack_time($player_id, $sql=null) {
	if (!$sql) {
		$sql = new DBAccess();
	}

	$update_last_attacked = "UPDATE players SET last_started_attack = now() WHERE player_id = ".intval($player_id);
	$sql->Query($update_last_attacked);  // updates the timestamp of the last_attacked column to slow excessive attacks.
}


/**
 * Checks whether an attack is legal or not.
 * DEPRECATED.
 *
 */
function attack_legal()  //  Checks for errors in the initial stage of combat.
{
	global $attacked, $target, $attacker_turns, $required_turns, $attacker, $player_id, $sql;
	global $target_ip, $attacker_ip, $target_confirmed, $attacker_health, $target_health, $target_status;

	$second_interval_limiter_on_attacks = '.20';

	$sel_last_started_attack = "SELECT player_id FROM players
  	WHERE player_id = '".intval($player_id)."'
  	AND ((now() - interval '".$second_interval_limiter_on_attacks." second') >= last_started_attack) LIMIT 1";

	$attack_later_than_limit = $sql->QueryItem($sel_last_started_attack);
	// Returns a player id if the enough time has passed, or else or false/null.

	// *** TODO: Take in and require energy in this function.

	$target_id = get_user_id($target);
	$defender_id = get_user_id($attacker);

	//  *** START OF ILLEGAL ATTACK ERROR LIST  ***
	if (!$attack_later_than_limit) {
		echo "You cannot attack more than five times in a second.";
		return false;
	} else if ($attacked != 1) {
		return false;
	} else if ($target == "") {
		echo "Your victim does not exist.<br>\n";
		return false;
	} else if ($target == $attacker) {
		echo "Commiting suicide is a tactic reserved for samurai.<br>\n";
		return false;
	} else if ($user_turns < $required_turns) {
		echo "You do not have enough turns to execute the attack you chose, use a speed scroll or wait for more turns on the half hour.<br>\n";
		return false;
	} else if  ($target_ip == $_SESSION['ip'] && $_SESSION['ip'] != '127.0.0.1') {
		echo "You can not attack a ninja from the same domain.<br>\n";
		return false;
	} else if ($target_confirmed == 0) {
		echo "You can not attack an inactive ninja.<br>\n";
		return false;
	} else if ($target_health < 1) {
		echo "You can not attack a corpse.<br>\n";
		return false;
	} else if ($target_status['Stealth']) {
		echo "Your victim is stealthed. You cannot attack this ninja by normal means.<br>\n";
		return false;
	} else if (($targetClan = get_clan_by_player_id($target_id)) && ($defenderClan = get_clan_by_player_id($defender_id))) {
		if ($targetClan->getID() == $defenderClan->getID()) {
			echo "You cannot attack a ninja in the same clan as you.\n";
			return false;
		} else {
			return true;	// *** ATTACK IS LEGAL ***
		}
	} else {
		return true;  //  ***  ATTACK IS LEGAL ***
	}
}

function killpointsFromDueling()  //  *** Multiple Killpoints from Dueling ***
{
  global $target_level,$attacker_level,$starting_target_health,$killpoints,$duel;

  $levelDifference=($target_level-$attacker_level);

  switch (TRUE)
  {
	case ($levelDifference>10): //Level difference greater than 10 gives a max return of 5 killpoints.
	  $levelDifferenceMultiplier=5;
	break;
	case ($levelDifference>0): //Level diff between 1 and 10.
	  $levelDifferenceMultiplier=ceil($levelDifference/2);  //killpoint return of half the level difference.
	break;
	default:
	  $levelDifferenceMultiplier=0;
	break;
  }

/*
  if ($levelDifference>10)  //Level difference greater than 10 gives a max return of 5 killpoints.
  {
    $levelDifferenceMultiplier=5;
  }
  elseif ($levelDifference>0)  //Level diff between 1 and 10.
  {
    $levelDifferenceMultiplier=floor($levelDifference/2);  //killpoint return of half the level difference.
  }
  else  //When the level difference is negative or both are of the same level.
  {
    $levelDifferenceMultiplier=0;
  }
*/

  $killpoints = 1+$levelDifferenceMultiplier;
}

function preBattleStats()
{
  global $target,$target_health,$attacker_health, $target_str,$attacker;

  echo "<table border=\"0\">\n";
  echo "<tr>\n";
  echo "  <th colspan=\"3\">\n";
  echo "  Before the Attack\n";
  echo "  </th>\n";
  echo "</tr>\n";

  echo "<tr>\n";
  echo "  <td>\n";
  echo "  Name\n";
  echo "  </td>\n";

  echo "  <td>\n";
  echo "  STR\n";
  echo "  </td>\n";

  echo "  <td>\n";
  echo "  HP\n";
  echo "  </td>\n";
  echo "</tr>\n";

  echo "<tr>\n";
  echo "  <td>\n";
  echo "  $attacker\n";
  echo "  </td>\n";

  echo "  <td>\n";
  echo    getStrength($attacker)."\n";
  echo "  </td>\n";

  echo "  <td>\n";
  echo "<span style=\"color:brown;font-weight:normal;\">";
  echo    $attacker_health."\n";
  echo "</span>";
  echo "  </td>\n";
  echo "</tr>\n";

  echo "<tr>\n";
  echo "  <td>\n";
  echo    $target."\n";
  echo "  </td>\n";

  echo "  <td>\n";
  echo    $target_str."\n";
  echo "  </td>\n";

  echo "  <td>\n";
  echo    $target_health."\n";
  echo "  </td>\n";
  echo "</tr>\n";

  echo "</table>\n";

  echo "<hr>\n";
}


function finalResults()
{
  global $total_attacker_damage,$total_target_damage,$target,$attacker_health,$target_health,$attacker;

  echo "<table style=\"border: 0;\">\n";
  echo "<tr>\n";
  echo "  <th colspan=\"3\">\n";
  echo "  Results of the Attack\n";
  echo "  </th>\n";
  echo "</tr>\n";

  echo "<tr>\n";
  echo "  <td>\n";
  echo "  Name\n";
  echo "  </td>\n";

  echo "  <td>\n";
  echo "  Total Dmg\n";
  echo "  </td>\n";

  echo "  <td>\n";
  echo "  HP\n";
  echo "  </td>\n";
  echo "</tr>\n";

  echo "<tr>\n";
  echo "  <td>\n";
  echo "  $attacker\n";
  echo "  </td>\n";

  echo "  <td style=\"text-align: center;\">\n";
  echo    $total_attacker_damage."\n";
  echo "  </td>\n";

  echo "  <td>\n";
  $finalizedHealth=$attacker_health-$total_target_damage;
  if ($finalizedHealth<100)  // Makes your health red if you go below 100 hitpoints.
  {
	echo "<span style=\"color:red;font-weight:bold;\">";
  }
  else // Normal text color for health.
  {
  	echo "<span style=\"color:brown;font-weight:normal;\">";
  }
  echo    $finalizedHealth."\n";
  echo "</span>";
  echo "  </td>\n";
  echo "</tr>\n";

  echo "<tr>\n";
  echo "  <td>\n";
  echo    $target."\n";
  echo "  </td>\n";

  echo "  <td style=\"text-align: center;\">\n";
  echo    $total_target_damage."\n";
  echo "  </td>\n";

  echo "  <td>\n";
  echo    $target_health-$total_attacker_damage."\n";
  echo "  </td>\n";
  echo "</tr>\n";
  echo "</table>";

  echo "<hr>\n";
}

?>
