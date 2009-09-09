<?php
// Provides backend system for mail sending based on input sent in.
$alive      = false;
$private    = true;
$quickstat  = false;
$page_title = "Send Mail";

include SERVER_ROOT."interface/header.php";
?>

<span class="brownHeading">Mail</span>

<br><br>

<?php
$to = in('to');
$messenger = in('messenger'); // naive spam detection attempt
$message   = in('message', null, 'toMessage'); // Special filtering to a message.
$username = get_username();

if ($to == "") { die("This message has no recipient.\n");}
if ($message == "") {die("Your message was blank.\n");}

if ($messenger == 1){
  if ($to == "SysMsg") {die("SysMsg is a bot, do not email SysMsg.\n");}

	if ($to == "clansend") {
	  $message = "CLAN: $message"; // Don't filter because it needs to be saved first.
      $clan = getClan($username);
      $sql->Query("SELECT uname FROM players WHERE clan = '$clan'");
	  $resultSet = $sql->fetchAll(); // *** Store the result set.
	  foreach($resultSet as $loopClanMember) {
        	$clan_member = $loopClanMember['uname'];
        	sendMessage($username,$clan_member,$message);
        	echo "Sending mail to: ".out($clan_member)."<br>\n";
        }
		die("<br><a href=\"mail_read.php\">Go to Your Mail</a><br><br><a href=\"clan.php\">Return to Clan Options</a>\n");
	}

    if ($to != "clansend") {
        $message_with_identifier = "MESSAGE: ".$message; // Don't filter when sending.
        sendMessage($username,$to,$message_with_identifier);
    }

  echo "A messenger takes your message and will deliver your mail <br>
    From: ".out($username)." <br>TO: ".out($to)." <br>Message: ".out($message)."<br><br>\n";
  echo "<a href=\"player.php?player=".urlencode($to)."\">Return to Player Detail</a>";
} else {
  "You need to give your message to a <a href=\"mail.php\">messenger</a> for delivery.";
}

include SERVER_ROOT."interface/footer.php";
?>
