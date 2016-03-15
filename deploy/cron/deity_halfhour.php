<?php
require_once(substr(__FILE__, 0, (strpos(__FILE__, 'cron/'))).'resources.php');
require_once(CORE.'base.inc.php');
require_once(LIB_ROOT."control/lib_deity.php"); // Deity-specific functions

use NinjaWars\core\data\DatabaseConnection;

$logMessage = "DEITY_HALFHOUR STARTING: ".date(DATE_RFC1036)."\n";

$score                = get_score_formula();

DatabaseConnection::getInstance();
DatabaseConnection::$pdo->query('BEGIN TRANSACTION');
DatabaseConnection::$pdo->query("UPDATE players SET turns = 0 WHERE turns < 0"); // if anyone has less than 0 turns, set it to 0

$s = DatabaseConnection::$pdo->prepare("UPDATE players SET turns = turns+:rate WHERE turns < :threshold");  // add turns at the regen rate for anyone below the threshold
$s->bindValue(':rate', TURN_REGEN_PER_TICK);
$s->bindValue(':threshold', TURN_REGEN_THRESHOLD);
$s->execute();

DatabaseConnection::$pdo->query("UPDATE players SET bounty = 0 WHERE bounty < 0"); // if anyone has negative bounty, set it to 0

$inactivity = DatabaseConnection::$pdo->prepare("DELETE FROM ppl_online WHERE activity < (now() - :maxtime::interval)");
$inactivity->bindValue(':maxtime', ONLINE_TIMEOUT);
$inactivity->execute();

$out_display['Inactive Browsers Deactivated'] = $inactivity->rowCount();

// *** HEAL Characters a certain amount ***

heal_characters(); // Just use the defaults here.

// **************
// Visual output:

foreach ($out_display AS $loopKey => $loopRowResult) {
    $logMessage .= "DEITY_HALFHOUR: Result type: $loopKey yeilded result number: $loopRowResult\n";
}

$logMessage .= "DEITY_HALFHOUR ENDING: ".date(DATE_RFC1036)."\n";

$log = fopen(LOGS.'deity.log', 'a');
fwrite($log, $logMessage);
fclose($log);
