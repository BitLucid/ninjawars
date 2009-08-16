<?php
require_once('../lib/base.inc.php');
require_once(LIB_ROOT."specific/lib_deity.php"); // Deity-specific functions

$score = get_score_formula();

/// @ TODO - This script should be secured.
$sql = new DBAccess();

// ******************* INITIALIZATION ******************************
$poisonHealthDecrease = 50;			// *** The amount that poison decreases health each half-hour.
$maximum_heal         = 150;
$maximum_turns        = 300;		// *** Turn # beyond which you will drop back down to, though normal turn increase stops earlier.
$maxtime              = '6 hours';	// *** Max time a person is kept online without being active.
$turn_regen_threshold = 100;

$out_display = array();

// ******************* END OF CONSTANTS ***********************
//sendChat("----","ChatMsg","----".date("h:i")."----"); // Halfhour message

$sql->Update("UPDATE time SET amount = amount+1 WHERE time_label='hours'"); // Update the hours ticker.
$sql->Update("UPDATE time SET amount = 0 WHERE time_label='hours' AND amount>=24"); // Rollover the time to hour zero.
$sql->Update("UPDATE players SET turns = 0 WHERE turns < 0");
$sql->Update("UPDATE players SET bounty = 0 WHERE bounty < 0");
$sql->Update("UPDATE players SET turns = turns+1 WHERE class ='Blue' and turns < ".$turn_regen_threshold);         // Blue turn code
$sql->Update("UPDATE players SET turns = turns+2 where turns < ".$turn_regen_threshold);   // add 2 turns on the hour, up to 100.

// Database connection information here
$sql->Query("DELETE FROM ppl_online WHERE activity < (now() - interval '".$maxtime."')");
//Skip error logging this for now. $out_display['Inactive Browsers Deactivated'] = $sql->a_rows;

// *** HEAL ***
$sql->Update
(
	"UPDATE players SET health=numeric_smaller(health+8, $maximum_heal) ".
	     "WHERE health BETWEEN 1 AND $maximum_heal AND NOT ".
		 "cast(status&".POISON." AS boolean)"
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
$params = array('full_max'=>50, 'minor_revive_to'=>150, 'major_revive_percent'=>5);
$resurrected = revive_players($params);
/* @params array('full_max'=>80, 'minor_revive_to'=>100, 'major_revive_percent'=>5,
 *      'just_testing'=>false)
*/
// $out_display['Players Resurrected'] = reset($resurrected);
// $out_display['Total Dead'] = end($resurrected);

// Ranking gets done in the 5 minute one now, so no need for it here.
// ***********************

// previously: CASE WHEN health-10 < 0 THEN health*(-1) ELSE 10 END
$sql->Update("UPDATE players SET health = numeric_larger(0, health-$poisonHealthDecrease) WHERE health>0 AND CAST((status&".POISON.") AS bool)"); // *** poisoned takes away life ***

$sql->Update("UPDATE players SET health = 0 WHERE health < 0"); // *** zeros negative health totals.
$sql->Update("UPDATE players SET turns = ".$maximum_turns." WHERE turns > ".$maximum_turns); // max turn limiter gets run from the constants section.

$sql->Update("UPDATE players SET status = status-".FROZEN." WHERE cast(status&".FROZEN." AS bool)"); // Cold Steal Crit Fail Unfreeze
$sql->Update("UPDATE players SET status = status-".STEALTH."  WHERE cast(status&".STEALTH." AS bool)"); //stealth lasts 1 hr


$logMessage = "DEITY_HOULRY STARTING: ".date(DATE_RFC1036)."\n";

// **************
// Visual output:

foreach ($out_display AS $loopKey => $loopRowResult)
{
    $logMessage .= "DEITY_HOULRY: Result type: $loopKey yeilded result: $loopRowResult\n";
}

$logMessage .= "DEITY_HOULRY ENDING: ".date(DATE_RFC1036)."\n";

$log = fopen(LOGS.'deity.log', 'a');
fwrite($log, $logMessage);
fclose($log);
?>
