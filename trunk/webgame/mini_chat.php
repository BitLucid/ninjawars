<?php
$private    = false;
$alive      = false;
$page_title = "Mini Chat";
$quickstat  = false;

include "interface/header.php";
?>

<script type="text/javascript">
function refreshpage()
{
  parent.mini_chat.location="mini_chat.php";
}
setInterval("refreshpage()",300*1000);
</script>

<?php
$default_limit = 22;
$chatlength = in('chatlength', $default_limit, 'toInt');
$filteredMessage = in('message', null, 'forChat'); // *** Sanitize any message
$command = in('command');
$sentMessage = in('message');
$sent = false;

echo "<div id='mini-chat'>";
if (is_logged_in()) {
	if ($command == "postnow" && $filteredMessage) {
	  sendChat($username,'ChatMsg',$filteredMessage);
	  $sent = true;
	  $command = "";
	  $sentMessage = "";
	}
	echo "<form id=\"post_msg\" action=\"mini_chat.php\" method=\"post\" name=\"post_msg\" style=\"margin-top:0em;margin-bottom:0.5em\">\n";
	echo "<input id=\"message\" type=\"text\" size=\"20\" maxlength=\"490\" name=\"message\" class=\"textField\" />\n";
	echo "<input id=\"command\" type=\"hidden\" value=\"postnow\" name=\"command\" />";
	echo "<input type=\"submit\" value=\"Send\" class=\"formButton\" />\n";
	if($sent){
	    echo "<span style=font-size:85%>Sent.</span></br />\n";
	}
	echo "</form>\n";// *** Makes the "sent" notice appear within the form.
} else {
    // *** Not logged in.
	echo "<p class='notice'>Not currently logged in to chat.</p>";
}

$show_elipsis = false;
$messageCount = $sql->QueryItem("select count(*) from chat");
if ($messageCount>$chatlength){
	$show_elipsis = true;
}

$members = $sql->QueryItem("select count(*) from ppl_online where member = true AND activity > (now() - CAST('30 minutes' AS interval))");
$membersTotal = $sql->QueryItem("select count(*) from ppl_online where member = true");

echo "<div style='border-top:thin dotted teal;'>";
echo "Active Members:  ".($members?$members : '0')." / ".($membersTotal?$membersTotal : '0')." \n";
echo "</div>";

echo "<div id='chatMessages' style='float:;margin-top:0em'>";
$sql->Query("SELECT send_from, message FROM chat ORDER BY id DESC LIMIT $chatlength");// Pull messages
/*
* SPEED PROBLEM AREA.
* This query could probably use further limitations to speed up the chat page load time.
*/
$chats = $sql->fetchAll();
foreach($chats AS $messageData) {
	// *** PROBABLY ALSO A SPEED PROBLEM AREA.
	$from = $messageData['send_from'];
	// *** Since the message only gets displayed once, just echo it directly.
	echo "[<a href=\"player.php?player=$from\" target=\"main\">$from</a>]: ".$messageData['message']."<br />\n";
}
if ($show_elipsis){ // to indicate there are more chats available
	echo ".<br />.<br />.<br />";
}
/*if ($chatlength != 360)
{
	echo "<br /><a href=\"mini_chat.php?chatlength=360\" style=font-size:90%>View remaining messages.</a><br />\n";
}*/
echo "</div></div>";
?>
