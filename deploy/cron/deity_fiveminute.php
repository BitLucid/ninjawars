<?php
require_once(dirname(__FILE__).'/../lib/base.inc.php'); // Currently this forces crons locally to be called from the cron folder.
require_once(LIB_ROOT."specific/lib_deity.php"); // Deity-specific functions

DatabaseConnection::getInstance();
DatabaseConnection::$pdo->query('TRUNCATE player_rank');
DatabaseConnection::$pdo->query("SELECT setval('player_rank_rank_id_seq1', 1, false)");
$ranked_players = DatabaseConnection::$pdo->query('INSERT INTO player_rank (_player_id, score) SELECT player_id, ((level*5000) + floor(gold/200) + (CASE WHEN kills > (5*level) THEN 3000 + least(floor((kills - (5*level)) * .3), 2000) ELSE ((kills/(5*level))*3000) END) - (days*200)) AS score FROM players WHERE confirmed = 1 ORDER BY score DESC');

// *** Running from a cron script, we don't want any output unless we have an error ***

$rand = rand(1, 12);

if ($rand == 1) {
	// Only log fiveminute log output randomly about once an hour to cut down on
	// spam in the log.  This log message isn't very important anyway.

	$out_display['Ranked Players'] = $ranked_players->rowCount();

	// ***********
	// Log output:

	$logMessage = 'DEITY_FIVEMINUTE STARTING: '.date(DATE_RFC1036)."\n";

	foreach ($out_display AS $loopKey => $loopRowResult) {
		$logMessage .= "DEITY_FIVEMINUTE: Result type: $loopKey yeilded result number: $loopRowResult \n";
	}

	$logMessage .= 'DEITY_FIVEMINUTE ENDING: '.date(DATE_RFC1036)."\n";

	$log = fopen(LOGS.'deity.log', 'a');
	fwrite($log, $logMessage);
	fclose($log);
}
?>
