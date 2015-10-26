<?php
require_once(substr(__FILE__, 0, (strpos(__FILE__, 'cron/'))).'resources.php');
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

$out_display = array();

// ******************* END OF CONSTANTS ***********************

DatabaseConnection::$pdo->query('BEGIN TRANSACTION');
DatabaseConnection::$pdo->query("UPDATE time SET amount = amount+1 WHERE time_label = 'hours'"); // Update the hours ticker.
DatabaseConnection::$pdo->query("UPDATE time SET amount = 0 WHERE time_label = 'hours' AND amount >= 24"); // Rollover the time to hour zero.
DatabaseConnection::$pdo->query("UPDATE players SET turns = 0 WHERE turns < 0");
DatabaseConnection::$pdo->query("UPDATE players SET bounty = 0 WHERE bounty < 0");
$s = DatabaseConnection::$pdo->prepare("UPDATE players SET turns = turns+1 FROM class_skill JOIN skill ON skill_id = _skill_id WHERE turns < :threshold AND _skill_id = 3 AND class_skill._class_id = players._class_id AND level >= coalesce(class_skill_level, skill_level)");	// *** Speed skill turn gain code, replaces Blue/Crane turn gain code ***
$s->bindValue(':threshold', TURN_REGEN_THRESHOLD);
$s->execute();

$s = DatabaseConnection::$pdo->prepare("UPDATE players SET turns = turns+:regen_rate WHERE turns < :threshold"); // add 2 turns on the hour, up to 100.
$s->bindValue(':threshold', TURN_REGEN_THRESHOLD);
$s->bindValue(':regen_rate', TURN_REGEN_PER_TICK);
$s->execute();

//Skip error logging this for now. $out_display['Inactive Browsers Deactivated'] = $inactivity->rowCount();

// *** HEAL ***
$s = DatabaseConnection::$pdo->prepare(
	"UPDATE players SET health = numeric_smaller(health+:regen_rate, :max_heal) ".
	     "WHERE health BETWEEN 1 AND :max_heal2 AND NOT ".
		 "CAST(status&:poison AS boolean)"
);
$s->bindValue(':max_heal', HEALTH_REGEN_THRESHOLD);
$s->bindValue(':max_heal2', HEALTH_REGEN_THRESHOLD);
$s->bindValue(':regen_rate', HEALTH_REGEN_PER_TICK);
$s->bindValue(':poison', POISON);
$s->execute();

assert(POISON != 'POISON');
$s = DatabaseConnection::$pdo->prepare("UPDATE players SET health = numeric_larger(0, health-:damage) WHERE health > 0 AND CAST((status&:poison) AS bool)"); // *** poisoned takes away life ***
$s->bindValue(':damage', POISON_DAMAGE);
$s->bindValue(':poison', POISON);
$s->execute();

DatabaseConnection::$pdo->query("UPDATE players SET health = 0 WHERE health < 0"); // *** zeros negative health totals.
$s = DatabaseConnection::$pdo->prepare("UPDATE players SET turns = :max_turns WHERE turns > :max_turns2"); // max turn limiter gets run from the constants section.
$s->bindValue(':max_turns', MAX_TURNS);
$s->bindValue(':max_turns2', MAX_TURNS);
$s->execute();

assert(FROZEN != 'FROZEN'); // These constants should be numeric.
assert(STEALTH != 'STEALTH');
$s = DatabaseConnection::$pdo->prepare("UPDATE players SET status = status-:frozen WHERE CAST(status&:frozen2 AS bool)"); // Cold Steal Crit Fail Unfreeze
$s->bindValue(':frozen', FROZEN);
$s->bindValue(':frozen2', FROZEN);
$s->execute();

$s = DatabaseConnection::$pdo->prepare("UPDATE players SET status = status-:stealth WHERE CAST(status&:stealth2 AS bool)"); //stealth lasts 1 hr
$s->bindValue(':stealth', STEALTH);
$s->bindValue(':stealth2', STEALTH);
$s->execute();
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
