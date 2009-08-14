<?php
/*
 * Functions for use in the deities.
 *
 * @package deity
 * @subpackage deity_lib
 */
/*include SERVER_ROOT."interface/header.php";*/
require_once(LIB_ROOT.'specific/lib_deity.php');
//


// *************** DEITY NIGHTLY, manual-run-output occurs at the bottom.*********************

error_log("DEITY_NIGHTLY: Deity reset occurred at server date/time: ".date('l jS \of F Y h:i:s A').".");

$sql = new DBAccess();
$affected_rows['Increase Days Of Players'] = update_days($sql);

//$sql->Update("UPDATE players SET status = status-".POISON." WHERE status&".POISON);  // Black Poison Fix
//$sql->Update("UPDATE players SET status = 0");  // Should just get rid of all status effects.
//$affected_rows[2] = $sql->a_rows;


$deleted = shorten_chat($sql); // run the shortening of the chat.
$affected_rows['deleted chats']=$deleted;
sendChat(CHAT_TIME_NAME,"ChatMsg","----".date("F j, Y")."----"); // Display the date change.
error_log("DEITY_NIGHTLY: Chats deleted (if a deletion value is returned): ".$deleted);

$stats = membership_and_combat_stats($sql, $update_past_stats=true);
$affected_rows['Vicious killer: '] = $stats['vicious_killer'];
error_log("DEITY_NIGHTLY: Vicious killer at reset was: (".$stats['vicious_killer'].")");
//$sql->Update("DELETE FROM mail WHERE send_to='SysMsg'");  //Deletes any mail directed to the sysmsg message bot.

//Nightly Unconfirm old players script settings.
$keep_players_until_over_the_number = 2600;
$days_players_have_to_be_older_than_to_be_unconfirmed = 60;
$maximum_players_to_unconfirm = 30;
$unconfirmed = unconfirm_older_players_over_minimums($keep_players_until_over_the_number, $days_players_have_to_be_older_than_to_be_unconfirmed, $maximum_players_to_unconfirm, $just_testing=false);
assert($unconfirmed < 21);
error_log('DEITY_NIGHTLY: Players unconfirmed: ('.$unconfirmed.').  30 is the current default maximum.');
$affected_rows['Players Unconfirmed'] = ($unconfirmed === false? 'Under the Minimum number of players' : $unconfirmed);

function delete_old_mail($sql, $limit = 50000){
	$sql->Update("DELETE FROM mail
		where (extract(month from CURRENT_TIMESTAMP) - extract(month from date))>2");  //Deletes mail older than 2 months.
	return $sql->a_rows;
}
$affected_rows['Old Mail Deletion'] =  delete_old_mail($sql);
error_log('DEITY_NIGHTLY: Mail deleted: ('.$affected_rows['Old Mail Deletion'].')');


// VISUAL OUTPUT
?> <span style="font-weight: bold;color: red;">Deity Nightly</span> <?php
foreach ($affected_rows AS $loopKey => $loopRowResult)
{
    echo "<br>Result type: ".$loopKey." yeilded result number: ".$loopRowResult;
}

/*include SERVER_ROOT."interface/footer.php";*/
// */
?>
