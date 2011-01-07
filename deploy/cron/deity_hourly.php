<?php
require_once(substr(__FILE__, 0, (strpos(__FILE__, 'lib/'))).'resources.php');
require_once(LIB_ROOT.'data/DatabaseConnection.php');
require_once(LIB_ROOT.'environment/lib_assert.php');
require_once(LIB_ROOT.'environment/status_defines.php'); // Status constant definitions.
require_once(LIB_ROOT.'environment/lib_error_reporting.php');
require_once(LIB_ROOT.'data/lib_db.php');
require_once(LIB_ROOT."control/lib_deity.php"); // Deity-specific functions

$logMessage = "DEITY_HOURLY STARTING: ".date(DATE_RFC1036)."\n";

$score = get_score_formula();

// Note that this script should not be web-accessible.
DatabaseConnection::getInstance();

// ******************* INITIALIZATION ******************************
$poisonHealthDecrease = 50;			// *** The amount that poison decreases health each half-hour.
$maximum_heal         = 200;
$maximum_turns        = 300;		// *** Turn # beyond which you will drop back down to, though normal turn increase stops earlier.
$maxtime              = '6 hours';	// *** Max time a person is kept online without being active.
$turn_regen_threshold = 100;

$out_display = array();

// ******************* END OF CONSTANTS ***********************

DatabaseConnection::$pdo->query('BEGIN TRANSACTION');
DatabaseConnection::$pdo->query("UPDATE time SET amount = amount+1 WHERE time_label = 'hours'"); // Update the hours ticker.
DatabaseConnection::$pdo->query("UPDATE time SET amount = 0 WHERE time_label = 'hours' AND amount >= 24"); // Rollover the time to hour zero.
DatabaseConnection::$pdo->query("UPDATE players SET turns = 0 WHERE turns < 0");
DatabaseConnection::$pdo->query("UPDATE players SET bounty = 0 WHERE bounty < 0");
DatabaseConnection::$pdo->query("UPDATE players SET turns = turns+1 FROM class_skill JOIN skill ON skill_id = _skill_id WHERE turns < $turn_regen_threshold AND _skill_id = 3 AND class_skill._class_id = players._class_id AND level >= coalesce(class_skill_level, skill_level)");	// *** Speed skill turn gain code, replaces Blue/Crane turn gain code ***

//DatabaseConnection::$pdo->query("UPDATE players SET turns = turns+1 WHERE _class_id = 2 AND turns < ".$turn_regen_threshold); // Blue/Crane turn code
DatabaseConnection::$pdo->query("UPDATE players SET turns = turns+2 WHERE turns < ".$turn_regen_threshold); // add 2 turns on the hour, up to 100.

//In activity is being handled by half-hourly right now, no need to run this query in both scripts.
//$inactivity = DatabaseConnection::$pdo->query("DELETE FROM ppl_online WHERE activity < (now() - interval '".$maxtime."')");

//Skip error logging this for now. $out_display['Inactive Browsers Deactivated'] = $inactivity->rowCount();

// *** HEAL ***
DatabaseConnection::$pdo->query(
	"UPDATE players SET health = numeric_smaller(health+8, $maximum_heal) ".
	     "WHERE health BETWEEN 1 AND $maximum_heal AND NOT ".
		 "CAST(status&".POISON." AS boolean)"
);

// ****************************** RESURRECTION CHECK, DEPENDENT UPON RESURRECTION_TIME ****************************
/* OLD System
$minimum = 2;
$by_percent = true;
$maximum = 4;

$resurrect_info = revive_appropriate_players($minimum, $maximum, $by_percent, $just_testing=false);
assert($resurrect_info['revived']<$resurrect_info['target_number']);
*/
// New system, potentially move to the halfhour, and then half the major_revive_percent?
$params = array('full_max'=>75, 'minor_revive_to'=>300, 'major_revive_percent'=>15);
$resurrected = revive_players($params);
/* @params array('full_max'=>80, 'minor_revive_to'=>100, 'major_revive_percent'=>5,
 *      'just_testing'=>false)
*/
// $out_display['Players Resurrected'] = reset($resurrected);
// $out_display['Total Dead'] = end($resurrected);

// Ranking gets done in the 5 minute one now, so no need for it here.
// ***********************

// previously: CASE WHEN health-10 < 0 THEN health*(-1) ELSE 10 END
assert(POISON != 'POISON');
DatabaseConnection::$pdo->query("UPDATE players SET health = numeric_larger(0, health-$poisonHealthDecrease) WHERE health > 0 AND CAST((status&".POISON.") AS bool)"); // *** poisoned takes away life ***

DatabaseConnection::$pdo->query("UPDATE players SET health = 0 WHERE health < 0"); // *** zeros negative health totals.
DatabaseConnection::$pdo->query("UPDATE players SET turns = ".$maximum_turns." WHERE turns > ".$maximum_turns); // max turn limiter gets run from the constants section.

assert(FROZEN != 'FROZEN'); // These constants should be numeric.
assert(STEALTH != 'STEALTH');
DatabaseConnection::$pdo->query("UPDATE players SET status = status-".FROZEN." WHERE CAST(status&".FROZEN." AS bool)"); // Cold Steal Crit Fail Unfreeze
DatabaseConnection::$pdo->query("UPDATE players SET status = status-".STEALTH."  WHERE CAST(status&".STEALTH." AS bool)"); //stealth lasts 1 hr
DatabaseConnection::$pdo->query('COMMIT');

// **************
// Visual output:

foreach ($out_display AS $loopKey => $loopRowResult) {
    $logMessage .= "DEITY_HOURLY: Result type: $loopKey yeilded result: $loopRowResult\n";
}

$logMessage .= "DEITY_HOURLY ENDING: ".date(DATE_RFC1036)."\n";

$log = fopen(LOGS.'deity.log', 'a');
fwrite($log, $logMessage);
fclose($log);
?>
