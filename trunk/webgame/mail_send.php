<?php
// Provides backend system for mail sending based on input sent in.
$alive      = false;
$private    = true;
$quickstat  = false;
$page_title = "Send Mail";

include "interface/header.php";
?>

<span class="brownHeading">Mail</span>

<br /><br />

<?php
$to = in('to');
$messenger = in('messenger');
$message   = in('message', null, 'toMessage'); // Special filtering to a message.

if ($to == "") { die("This message has no recipient.\n");}
if ($message == "") {die("Your message was blank.\n");}

if ($messenger == 1){
  if ($to == "SysMsg") {die("SysMsg is a bot, do not email SysMsg.\n");}

	if ($to == "clansend")
	{
	  $message = "CLAN: ".$message;
      $clan = getClan($_SESSION['username']);
      $sql->Query("SELECT uname FROM players WHERE clan = '$clan'");
	  $resultSet = $sql->fetchAll(); // *** Store the result set.
	  foreach($resultSet as $loopClanMember) {
        	$name = $loopClanMember['uname'];
        	echo "Sending mail to: $name<br />\n";
        	sendMessage($username,$name,$message,$filter=true);
        }

		die("<br /><a href=\"mail_read.php\">Go to Your Mail</a><br /><br /><a href=\"clan.php\">Return to Clan Options</a>\n");
	}
  
  if ($to != "clansend") 
	  {
	  $message_with_identifier = "MESSAGE: ".$message;
	  sendMessage($_SESSION['username'],$to,$message_with_identifier,$filter=true);
	  }

  echo "A messenger takes your message and will deliver your mail <br />From: ".$_SESSION['username']." <br />TO: $to <br />Message: $message<br /><br />\n";
  echo "<a href=\"player.php?player=$to\">Return to Player Detail</a>";
}
else
{
  "You need to give your message to a <a href=\"mail.php\">messenger</a> for delivery.";
}

include "interface/footer.php";
?>

