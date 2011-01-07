<?php
require_once(substr(__FILE__, 0, (strpos(__FILE__, 'lib/'))).'resources.php');
require_once(LIB_ROOT.'data/DatabaseConnection.php');
require_once(LIB_ROOT.'environment/lib_assert.php');
require_once(LIB_ROOT.'environment/status_defines.php'); // Status constant definitions.
require_once(LIB_ROOT.'environment/lib_error_reporting.php');
require_once(LIB_ROOT.'data/lib_db.php');
require_once(LIB_ROOT."control/lib_deity.php"); // Deity-specific functions

$logMessage = "DEITY_HALFHOUR STARTING: ".date(DATE_RFC1036)."\n";

$regen_rate           = 2; // Rate is for turns.
$turn_regen_threshold = 100;
$maximum_heal         = 200;
$maxtime              = '6 hours'; // *** Max time a person is kept online without being active.
$score                = get_score_formula();

DatabaseConnection::getInstance();
DatabaseConnection::$pdo->query('BEGIN TRANSACTION');
DatabaseConnection::$pdo->query("UPDATE players SET turns = 0 WHERE turns < 0"); // if anyone has less than 0 turns, set it to 0
DatabaseConnection::$pdo->query("UPDATE players SET turns = turns+$regen_rate WHERE turns < ".$turn_regen_threshold);  // add turns at the regen rate for anyone below the threshold
DatabaseConnection::$pdo->query("UPDATE players SET bounty = 0 WHERE bounty < 0"); // if anyone has negative bounty, set it to 0

$inactivity = DatabaseConnection::$pdo->query("DELETE FROM ppl_online WHERE activity < (now() - interval '".$maxtime."')");

$out_display['Inactive Browsers Deactivated'] = $inactivity->rowCount();

// *** HEAL CODE ***

DatabaseConnection::$pdo->query("UPDATE players SET health = numeric_smaller(health+8+cast(floor(level/10) AS int), ($maximum_heal+cast(level AS int)))
	     WHERE health BETWEEN 1 AND $maximum_heal AND NOT cast(status&".POISON." AS bool)");
DatabaseConnection::$pdo->query('COMMIT');
// Higher levels now heal faster.
// Higher levels should now also heal to a larger maximum, level dependent.  e.g. level 100 gets +50 in how many hitpoints they'll heal up to.

// **************
// Visual output:

foreach ($out_display AS $loopKey => $loopRowResult) {
    $logMessage .= "DEITY_HALFHOUR: Result type: $loopKey yeilded result number: $loopRowResult\n";
}

$logMessage .= "DEITY_HALFHOUR ENDING: ".date(DATE_RFC1036)."\n";

$log = fopen(LOGS.'deity.log', 'a');
fwrite($log, $logMessage);
fclose($log);
?>
