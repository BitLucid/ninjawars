<?php
$private    = true;
$alive      = false;
$quickstat  = false;
$page_title = "Send A Message";

include SERVER_ROOT."interface/header.php";
?>

<span class="brownHeading">Mail: Compose Message</span>

<p>
<?php
if (getClan($username) != "")
{
  echo "<div style=\"border: thin solid white;padding-top: 3;padding-left: 5;padding-bottom: 3;width: 250px;\">\n";
  echo "<div class='subtitle'>Clan Mail</div>\n";
  echo "<form id=\"clan_msg\" action=\"mail_send.php\" method=\"get\" name=\"clan_msg\">\n";
  echo "  <textarea id=\"message\" cols=\"25\" rows=\"5\" name=\"message\" class=\"textField\"></textarea>\n";
  echo "  <input id=\"to\" type=\"hidden\" value=\"clansend\" name=\"to\">\n";
  echo "  <input id=\"messenger\" type=\"hidden\" value=\"1\" name=\"messenger\">\n";
  echo "  <input type=\"submit\" value=\"Send\" class=\"formButton\">\n";
  echo "</form>\n";
  echo "</div><br><br>\n";
}
?>

Search for a Ninja to send them a message from their profile:
<form id="player_search" action="list_all_players.php" method="get" name="player_search">
<input id="searched" type="text" maxlength="50" name="searched" class="textField">
<input type="submit" value="Search for Ninja" class="formButton">
</form>

</p>

<?php
include SERVER_ROOT."interface/footer.php";
?>
