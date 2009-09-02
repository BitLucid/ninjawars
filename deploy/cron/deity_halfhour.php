<?php
require_once(dirname(__FILE__).'/../lib/base.inc.php'); // Currently this forces crons locally to be called from the cron folder.
require_once(LIB_ROOT."specific/lib_deity.php"); // Deity-specific functions

$regen_rate           = 1;
$turn_regen_threshold = 100;
$maximum_heal         = 150;
$maxtime              = '6 hours'; // *** Max time a person is kept online without being active.
$score                = get_score_formula();

$sql = new DBAccess();

// Chat the time if no-one chatted recently
chat_timer();

$sql->Update("UPDATE players SET turns = 0 WHERE turns < 0"); // if anyone has less than 0 turns, set it to 0
$sql->Update("UPDATE players SET turns = turns+$regen_rate WHERE turns < ".$turn_regen_threshold);  // add turns at the regen rate for anyone below the threshold
$sql->Update("UPDATE players SET bounty = 0 WHERE bounty < 0"); // if anyone has negative bounty, set it to 0

$sql->Query("DELETE FROM ppl_online WHERE activity < (now() - interval '".$maxtime."')");

$out_display['Inactive Browsers Deactivated'] = $sql->a_rows;

// *** HEAL CODE ***

$sql->Update("UPDATE players SET health = numeric_smaller(health+8+cast(floor(level/10) AS int), $maximum_heal)
	     WHERE health BETWEEN 1 AND $maximum_heal AND NOT cast(status&".POISON." AS bool)");
// Higher levels now heal faster.

$logMessage = "DEITY_HALFHOUR STARTING: ".date(DATE_RFC1036)."\n";

// **************
// Visual output:

foreach ($out_display AS $loopKey => $loopRowResult)
{
    $logMessage .= "DEITY_HALFHOUR: Result type: $loopKey yeilded result number: $loopRowResult\n";
}

$logMessage .= "DEITY_HALFHOUR ENDING: ".date(DATE_RFC1036)."\n";

$log = fopen(LOGS.'deity.log', 'a');
fwrite($log, $logMessage);
fclose($log);
?>
