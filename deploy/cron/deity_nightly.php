<?php
/*
 * Functions for use in the deities.
 *
 * @package deity
 * @subpackage deity_lib
 */
require_once('../lib/base.inc.php');
require_once(LIB_ROOT.'specific/lib_deity.php');

$keep_players_until_over_the_number                   = 2600;
$days_players_have_to_be_older_than_to_be_unconfirmed = 60;
$maximum_players_to_unconfirm                         = 30;

// *************** DEITY NIGHTLY, manual-run-output occurs at the bottom.*********************

$sql = new DBAccess();
$affected_rows['Increase Days Of Players'] = update_days($sql);

//$sql->Update("UPDATE players SET status = status-".POISON." WHERE status&".POISON);  // Black Poison Fix
//$sql->Update("UPDATE players SET status = 0");  // Should just get rid of all status effects.
//$affected_rows[2] = $sql->a_rows;

$deleted = shorten_chat($sql); // run the shortening of the chat.
$affected_rows['deleted chats'] = $deleted;
sendChat(CHAT_TIME_NAME,"ChatMsg","----".date("F j, Y")."----"); // Display the date change.

$stats = membership_and_combat_stats($sql, $update_past_stats=true);
$affected_rows['Vicious killer: '] = $stats['vicious_killer'];
//$sql->Update("DELETE FROM mail WHERE send_to='SysMsg'");  //Deletes any mail directed to the sysmsg message bot.

//Nightly Unconfirm old players script settings.
$unconfirmed = unconfirm_older_players_over_minimums($keep_players_until_over_the_number, $days_players_have_to_be_older_than_to_be_unconfirmed, $maximum_players_to_unconfirm, $just_testing=false);
assert($unconfirmed < 21);

$affected_rows['Players Unconfirmed'] = ($unconfirmed === false ? 'Under the Minimum number of players' : $unconfirmed);

function delete_old_mail($sql, $limit = 50000){
	$sql->Update("DELETE FROM mail
		where (extract(month from CURRENT_TIMESTAMP) - extract(month from date))>2");  //Deletes mail older than 2 months.
	return $sql->a_rows;
}

// Delete from inventory where owner is unconfirmed or non-existent.
$sql->QueryRow("Delete from inventory where owner in (SELECT owner FROM inventory LEFT JOIN players ON owner = uname WHERE confirmed = 0 OR uname is null GROUP BY owner)");
$affected_rows['deleted items'] = $sql->a_rows;
$affected_rows['Old Mail Deletion'] =  delete_old_mail($sql);

$logMessage = "DEITY_NIGHTLY STARTING: ".date(DATE_RFC1036)."\n";
$logMessage .= "DEITY_NIGHTLY: Deity reset occurred at server date/time: ".date('l jS \of F Y h:i:s A').".\n";
$logMessage .= 'DEITY_NIGHTLY: Mail deleted: ('.$affected_rows['Old Mail Deletion'].")\n";
$logMessage .= "DEITY_NIGHTLY: Items: ".$affected_rows['deleted items']."\n";
$logMessage .= 'DEITY_NIGHTLY: Players unconfirmed: ('.$unconfirmed.").  30 is the current default maximum.\n";
$logMessage .= "DEITY_NIGHTLY: Vicious killer at reset was: (".$stats['vicious_killer'].")\n";
$logMessage .= "DEITY_NIGHTLY: Chats deleted (if a deletion value is returned): $deleted\n";

// **************
// Visual output:

foreach ($out_display AS $loopKey => $loopRowResult)
{
    $logMessage .= "DEITY_NIGHTLY: Result type: $loopKey yeilded result number: $loopRowResult\n";
}

$logMessage .= "DEITY_NIGHTLY ENDING: ".date(DATE_RFC1036)."\n";

$log = fopen(LOGS.'deity.log', 'a');
fwrite($log, $logMessage);
fclose($log);
?>
