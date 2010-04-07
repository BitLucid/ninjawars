<?php
$alive      = false;
$private    = false;
$quickstat  = false;
$page_title = "Accept a New Clan Member";

include SERVER_ROOT."interface/header.php";
?>

<h1>Accept A New Clan Member</h1>

<hr>

<?php
$confirm     = in('confirm');
$username    = get_username();
$user_id     = get_user_id();
$clan        = get_clan_by_player_id($user_id);
$clan_joiner = in('clan_joiner');
$clan_joiner_name = getPlayerName($clan_joiner);
$agree       = in('agree');
$random      = rand(1001, 9990);

if (!$clan) {
	echo "You have no clan.";
} elseif (!$clan_joiner) {
	echo "There is no potential ninja specified, so the induction cannot occur.";
} else {
	echo "$clan_joiner_name has requested to join your clan, ".$clan->getName().".<br>\n";

	if (!$agree) {
		echo "<form action=\"clan_confirm.php?clan_id=".$clan->getID()."&amp;clan_joiner=$clan_joiner&amp;confirm=$confirm\" method=\"post\">\n";
		echo "  <div><input id=\"agree\" type=\"hidden\" name=\"agree\" value=\"1\"><input type=\"submit\" value=\"Accept Request\"></div>\n";
		echo "</form>";
	} else {
		DatabaseConnection::getInstance();
		$statement = DatabaseConnection::$pdo->prepare("SELECT confirm FROM players WHERE player_id = :player");
		$statement->bindValue(':player', $clan_joiner);
		$statement->execute();
		$check = $statement->fetchColumn();

		$statement = DatabaseConnection::$pdo->prepare("SELECT _clan_id FROM clan_player WHERE _player_id = :player");
		$statement->bindValue(':player', $clan_joiner);
		$statement->execute();
		$current_clan = $statement->fetchColumn();

		echo "<div style=\"border:1px solid #000000;font-weight: bold;\">\n";

		if ($current_clan != "") {
			echo "This member is already part of a clan.\n";
			echo "<br><br>\n";
			echo "<a href=\"".WEB_ROOT."\">Return to Main</a>\n";
		} else if (!$check) {
			echo "<p>No such ninja.</p>";
			echo "<p><a href=\"".WEB_ROOT."\">Return to Main</a></p>\n";
		} elseif ($confirm == $check && $agree > 0) {
			echo "Request Accepted.<br>\n";
			$statement = DatabaseConnection::$pdo->prepare("INSERT INTO clan_player (_clan_id, _player_id) VALUES (:clan, :player)");
			$statement->bindValue(':clan', $clan->getID());
			$statement->bindValue(':player', $clan_joiner);
			$statement->execute();

			$statement = DatabaseConnection::$pdo->prepare("UPDATE players SET confirm = :confirm WHERE player_id = :player");
			$statement->bindValue(':confirm', $random);
			$statement->bindValue(':player', $clan_joiner);
			$statement->execute();

			echo "<br>".htmlentities($clan_joiner_name)." is now a member of your clan.<hr>\n";
			send_message($user_id, $clan_joiner,"CLAN: You have been accepted into ".$clan->getName());
		} else {
			echo "This clan membership change can not be verified, please ask the ninja to request joining again.\n";
		}
	}
}	// End of else (when clan_name is available).
?>

<br><br>
<a href="<?php echo WEB_ROOT;?>">Return to Main ?</a>

<?php
include SERVER_ROOT."interface/footer.php";
?>
