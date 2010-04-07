<?php
$alive      = false;
$private    = false;
$quickstat  = false;
$page_title = "Accept a New Clan Member";

include SERVER_ROOT."interface/header.php";
require_once(LIB_ROOT.'specific/lib_clan.php');
?>

<h1>Accept A New Clan Member</h1>

<hr>

<?php
$confirm     = in('confirm');
$username    = get_username();
$user_id     = get_user_id();
$clan        = get_clan_by_player_id($user_id);
$clan_joiner = in('clan_joiner');
$clan_joiner_name = get_username($clan_joiner);
$agree       = in('agree');

$random      = rand(1001, 9990);




if (!($clan instanceof Clan)) {
	echo "<p>You have no clan.</p>";
} elseif (!$clan_joiner) {
	echo "<p>There is no potential ninja specified, so the induction cannot occur.</p>";
} else {
	echo "<p>$clan_joiner_name has requested to join your clan, ".$clan->getName().".</p>\n";

	if (!$agree) {
		echo "<form action=\"clan_confirm.php?clan_id=".$clan->getID()."&amp;clan_joiner=$clan_joiner&amp;confirm=$confirm\" method=\"post\">\n";
		echo "  <div><input id=\"agree\" type=\"hidden\" name=\"agree\" value=\"1\"><input type=\"submit\" value=\"Accept Request\"></div>\n";
		echo "</form>";
	} else {
	
	    $joiner_clan = get_clan_by_player_id($clan_joiner);
	    $joiner_current_clan = $joiner_clan instanceof Clan? $clan->getID() : null;
	    
	    $joiner_info = get_player_info($clan_joiner);
	    $joiner_confirmation_no = $joiner_info? $joiner_info['confirm'] : null;



		echo "<div style=\"border:1px solid #000000;font-weight: bold;\">\n";

		if ($joiner_current_clan != "") {
			echo "<p>This member is already part of a clan.</p>\n";
		} else if (!$joiner_confirmation_no) {
			echo "<p>No such ninja.</p>";
		} elseif ($confirm == $joiner_confirmation_no && $agree > 0) {
			echo "<p>Request Accepted.</p>\n";
			
			$clan_id = $clan->getID();
			$player_id = $clan_joiner;
			// Put the player into the clan.
			add_player_to_clan($player_id, $clan_id);
			

			echo "<p>".htmlentities($clan_joiner_name)." is now a member of your clan.</p><hr>\n";
			send_message($user_id, $clan_joiner,"CLAN: You have been accepted into ".$clan->getName());
		} else {
			echo "<p>This clan membership change can not be verified, please ask the ninja to request joining again.</p>\n";
		}
	}
}	// End of else (when clan_name is available).
?>

<div class='return-to-main-link'>
<a href="<?php echo WEB_ROOT;?>">Return to Main ?</a>
</div>

<?php
include SERVER_ROOT."interface/footer.php";
?>
