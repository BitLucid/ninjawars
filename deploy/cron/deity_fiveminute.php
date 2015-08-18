<?php
require_once(substr(__FILE__, 0, (strpos(__FILE__, 'cron/'))).'resources.php');
require_once(LIB_ROOT.'data/DatabaseConnection.php');
require_once(LIB_ROOT.'environment/lib_assert.php');
require_once(LIB_ROOT.'environment/status_defines.php'); // Status constant definitions.
require_once(LIB_ROOT.'environment/lib_error_reporting.php');
require_once(LIB_ROOT.'data/lib_db.php');
require_once(LIB_ROOT."control/lib_deity.php"); // Deity-specific functions

DatabaseConnection::getInstance();
DatabaseConnection::$pdo->query('BEGIN TRANSACTION');
DatabaseConnection::$pdo->query('TRUNCATE player_rank');
DatabaseConnection::$pdo->query("SELECT setval('player_rank_rank_id_seq1', 1, false)");
$ranked_players = DatabaseConnection::$pdo->query('INSERT INTO player_rank (_player_id, score) SELECT player_id, ((level*5000) + floor(gold/200) + (CASE WHEN kills > (5*level) THEN 3000 + least(floor((kills - (5*level)) * .3), 2000) ELSE ((kills/(5*level))*3000) END) - (days*200)) AS score FROM players WHERE active = 1 ORDER BY score DESC');

// *** Running from a cron script, we don't want any output unless we have an error ***

// Add 1 to player's ki when they've been active in the last 5 minutes.
query("update players set ki = ki + 1 where last_started_attack > (now() - interval '6 minutes')");
DatabaseConnection::$pdo->query('COMMIT');

// Err on the side of low revives for this five minute tick.
$params = array('full_max'=>95, 'minor_revive_to'=>70, 'major_revive_percent'=>7);
$resurrected = revive_players($params);

$rand = rand(1, 60);

if ($rand == 1) {
	// Only log fiveminute log output randomly about once every 6 hours to cut down on
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
