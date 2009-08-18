<?php
require_once('../lib/base.inc.php'); // Currently this forces crons locally to be called from the cron folder.
require_once(LIB_ROOT."specific/lib_deity.php"); // Deity-specific functions

$sql = new DBAccess();
$sql->Query("truncate player_rank");
$sql->Query("SELECT setval('player_rank_rank_id_seq1', 1, false);");
$sql->Query("insert into player_rank (_player_id, score) select player_id, ((level*900) + (CASE WHEN gold < 1 THEN 0 ELSE floor(gold/200) END) + (kills*3) - (days*5)) AS score from players WHERE confirmed = 1 ORDER BY score DESC");

#   Running from a cron script, we don't want any output unless we have an error

$out_display['Ranked Players'] = $sql->a_rows;

// ***********
// Log output:

$logMessage = "DEITY_FIVEMINUTE STARTING: ".date(DATE_RFC1036)."\n";

foreach ($out_display AS $loopKey => $loopRowResult)
{
	$logMessage .= "DEITY_FIVEMINUTE: Result type: $loopKey yeilded result number: $loopRowResult \n";
}

$logMessage .= "DEITY_FIVEMINUTE ENDING: ".date(DATE_RFC1036)."\n";

$log = fopen(LOGS.'deity.log', 'a');
fwrite($log, $logMessage);
fclose($log);
?>
