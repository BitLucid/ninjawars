<?php
require_once(substr(__FILE__, 0, (strpos(__FILE__, 'cron/'))).'resources.php');
require_once(LIB_ROOT.'data/DatabaseConnection.php');
require_once(LIB_ROOT.'environment/lib_assert.php');
require_once(LIB_ROOT.'environment/status_defines.php'); // Status constant definitions.
require_once(LIB_ROOT.'environment/lib_error_reporting.php');
require_once(LIB_ROOT.'data/lib_db.php');
require_once(LIB_ROOT."control/lib_deity.php"); // Deity-specific functions

use NinjaWars\core\data\DatabaseConnection;

DatabaseConnection::getInstance();
DatabaseConnection::$pdo->query('BEGIN TRANSACTION');
DatabaseConnection::$pdo->query('TRUNCATE player_rank RESTART IDENTITY');
DatabaseConnection::$pdo->query("SELECT setval('player_rank_rank_id_seq', 1, false)");

$ranked_players = DatabaseConnection::$pdo->prepare('INSERT INTO player_rank (_player_id, score) SELECT player_id, ((level*:level_weight) + floor(gold/:gold_weight) + (CASE WHEN kills > (5*level) THEN 3000 + least(floor((kills - (5*level)) * .3), 2000) ELSE ((kills/(5*level))*3000) END) - (days*:inactivity_weight)) AS score FROM players WHERE active = 1 ORDER BY score DESC');
$ranked_players->bindValue(':level_weight', RANK_WEIGHT_LEVEL);
$ranked_players->bindValue(':gold_weight', RANK_WEIGHT_GOLD);
$ranked_players->bindValue(':inactivity_weight', RANK_WEIGHT_INACTIVITY);
$ranked_players->execute();

// *** Running from a cron script, we don't want any output unless we have an error ***

// Add 1 to player's ki when they've been active in the last 5 minutes.
$s = DatabaseConnection::$pdo->prepare("update players set ki = ki + :regen_rate where last_started_attack > (now() - :interval::interval)");
$s->bindValue(':interval', KI_REGEN_TIMEOUT);
$s->bindValue(':regen_rate', KI_REGEN_PER_TICK);
$s->execute();

DatabaseConnection::$pdo->query('COMMIT');

// Err on the side of low revives for this five minute tick.
$params = [
	'minor_revive_to'      => MINOR_REVIVE_THRESHOLD,
	'major_revive_percent' => MAJOR_REVIVE_PERCENT,
];

$resurrected = revive_players($params);

$rand = rand(1, DEITY_LOG_CHANCE_DIVISOR);

if ($rand === 1) {
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
