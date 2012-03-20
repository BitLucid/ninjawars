<?php
/*
 * Functions for use in the deities.
 *
 * @package deity
 * @subpackage deity_lib
 */
require_once(substr(__FILE__, 0, (strpos(__FILE__, 'cron/'))).'resources.php');
require_once(LIB_ROOT.'data/DatabaseConnection.php');
require_once(LIB_ROOT.'environment/lib_assert.php');
require_once(LIB_ROOT.'environment/status_defines.php'); // Status constant definitions.
require_once(LIB_ROOT.'environment/lib_error_reporting.php');
require_once(LIB_ROOT.'data/lib_db.php');
require_once(LIB_ROOT."control/lib_deity.php"); // Deity-specific functions

$logMessage = "DEITY_NIGHTLY STARTING: ---- ".date(DATE_RFC1036)." ----\n";

// TODO: Profile the slowdown point(s) of this script.
// TODO: Need a levelling log deletion.
// TODO: When the message table is created, delete from mail more stringently.
// TODO: Set up a backup of the players table.

$keep_players_until_over_the_number                   = 1000;
$days_players_have_to_be_older_than_to_be_unconfirmed = 60;
$maximum_players_to_unconfirm                         = 200;

// *************** DEITY NIGHTLY, manual-run-output occurs at the bottom.*********************

DatabaseConnection::getInstance();
DatabaseConnection::$pdo->query('BEGIN TRANSACTION');
$affected_rows['Increase Days Of Players'] = update_days();

//DatabaseConnection::$pdo->query("UPDATE players SET status = status-".POISON." WHERE status&".POISON);  // Black Poison Fix
$status_removal = DatabaseConnection::$pdo->query("UPDATE players SET status = 0");  // Hmmm, gets rid of all status effects, we may want to make that not have that limitation, some day.
$affected_rows['Statuses Removed'] = $status_removal->rowCount();

$deleted = shorten_chat(); // run the shortening of the chat.
$affected_rows['deleted chats'] = $deleted;

update_most_vicious_killer_stat();// Update the vicious killer stat.

//Nightly Unconfirm old players script settings.
$unconfirmed = unconfirm_older_players_over_minimums($keep_players_until_over_the_number, $days_players_have_to_be_older_than_to_be_unconfirmed, $maximum_players_to_unconfirm, $just_testing=false);
assert($unconfirmed < $maximum_players_to_unconfirm+1);

$affected_rows['Players Unconfirmed'] = ($unconfirmed === false ? 'Under the Minimum number of players' : $unconfirmed);

// Delete from inventory where owner is unconfirmed or non-existent.
$deleted_items = DatabaseConnection::$pdo->query("DELETE FROM inventory WHERE owner IN (SELECT owner FROM inventory LEFT JOIN players ON owner = player_id WHERE uname IS NULL GROUP BY owner)");
$affected_rows['deleted items'] = $deleted_items->rowCount();

$deleted_items = DatabaseConnection::$pdo->query("delete from levelling_log where killsdate < (now() - interval '2 months')");
$affected_rows['deleted levelling_logs'] = $deleted_items->rowCount();

$deleted_mail = delete_old_messages(); // As per the mail function in lib_deity.
$deleted_events = delete_old_events();
$affected_rows['Old Messages Deletion'] = $deleted_mail;

$level_log_delete = DatabaseConnection::$pdo->query("delete from levelling_log where killsdate < now()- interval '3 months'");
$affected_rows['levelling log deletion'] = $level_log_delete->rowCount(); // Keep only the last 3 months of logs.

$duel_log_delete = DatabaseConnection::$pdo->query("delete from dueling_log where date != cast(now() AS date) AND date != cast(now() AS date)-1"); // Keep only the last two days of duels.
$affected_rows['dueling log deletion'] = $duel_log_delete->rowCount();

$level_1_delete = DatabaseConnection::$pdo->query("delete from players where active = 0 and level = 1 and created_date < (now() - interval '5 days')"); // Delete old level 1's.
DatabaseConnection::$pdo->query('COMMIT');
$affected_rows['old level 1 players deletion'] = $level_1_delete->rowCount(); 


$logMessage .= "DEITY_NIGHTLY: Deity reset occurred at server date/time: ".date('l jS \of F Y h:i:s A').".\n";
$logMessage .= 'DEITY_NIGHTLY: Mail deleted: ('.$affected_rows['Old Messages Deletion'].")\n";
$logMessage .= "DEITY_NIGHTLY: Items: ".$affected_rows['deleted items']."\n";
$logMessage .= 'DEITY_NIGHTLY: Players unconfirmed: ('.$unconfirmed.").  30 is the current default maximum.\n";
$logMessage .= "DEITY_NIGHTLY: Chats deleted (if a deletion value is returned): $deleted\n";

// **************
// Visual output:

foreach ($affected_rows AS $loopKey => $loopRowResult) {
    $logMessage .= "DEITY_NIGHTLY: Result type: $loopKey yeilded result: $loopRowResult\n";
}

$logMessage .= "DEITY_NIGHTLY ENDING: ---- ".date(DATE_RFC1036)." ---- \n";

$log = fopen(LOGS.'deity.log', 'a');
fwrite($log, $logMessage);
fclose($log);
?>