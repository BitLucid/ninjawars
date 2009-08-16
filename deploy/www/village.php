<?php
$private    = false;
$alive      = false;
$page_title = "In-Game Chat";
$quickstat  = false;

include SERVER_ROOT."interface/header.php";
?>
<span class="brownHeading">Chat Board</span> -

<?php
echo "<a href=\"village.php?chatlength=50\">Refresh</a>\n";
echo "<br>\n";

$command = in('command');
$message = in('message', null, 'forChat');
// *** If a message does get posted, it becomes sanitized.
$chatlength = in('chatlength', 100, 'toInt'); // Default to 100.
?>

<script type="text/javascript">
function refreshpage()
{
	parent.main.location="village.php?chatlength=30";
}
setInterval("refreshpage()",300*1000);
</script>

<?php
if (get_username())
{
	echo "<form id=\"post_msg\" action=\"village.php\" method=\"post\" name=\"post_msg\">\n";
	echo "<div>\n";
	echo "Message: <input id=\"message\" type=\"text\" size=\"40\" maxlength=\"1000\" name=\"message\" class=\"textField\">\n";
	echo "<input id=\"command\" type=\"hidden\" value=\"postnow\" name=\"command\">";
	echo "<input type=\"submit\" value=\"Send\" class=\"formButton\">\n";
	echo "</div>\n";
	echo "</form>\n";

	if ($command == "postnow" && $message != "")
	{
		sendChat($username, 'ChatMsg', $message, $filter=true); // *** Message gets filtered when taken in from the request.
		echo "Your post has been added.</br>\n";
		$command = "";
		$message = "";
		$chatlength = 30; // *** This one is short to decrease the load time.
	}
}
else // *** Not logged in.
{
	echo "<p style=\"color: maroon;\">Not currently logged in to chat.</p>";
}

$sql->Query("SELECT id, send_from, message FROM chat ORDER BY id DESC LIMIT $chatlength");

foreach($sql->fetchAll() /*CHAT MESSAGES */ as $messageLine)
{
	$from = $messageLine['send_from'];
	echo "[<a href=\"player.php?player=$from\" target=\"main\">$from</a>]: ".$messageLine['message']."<br>\n";
}

if ($chatlength != 360)
{
	echo "<br><a href=\"village.php?chatlength=360\">View the Rest of the Messages</a><br>\n";
}

include SERVER_ROOT."interface/footer.php";
?>
