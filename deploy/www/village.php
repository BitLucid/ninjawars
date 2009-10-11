<?php
require_once(LIB_ROOT."specific/lib_chat.php"); // Require all the chat helper and rendering functions.

$private    = false;
$alive      = false;
$page_title = "Chat Board";
$quickstat  = false;

include SERVER_ROOT."interface/header.php";

echo "<span class='brownHeading'>Chat Board</span> -";
echo "<a href=\"".$_SERVER['PHP_SELF']."?chatlength=50\">Refresh</a>\n";
echo "<br>\n";
echo "Message: ";

$default_limit = 360;
$chatlength = in('chatlength', $default_limit, 'toInt');
$message = in('message', null, 'forChat'); // Essentially no filtering.
$command = in('command');
$sentMessage = in('message');
$sent = false;
$username = get_username();
$input_form = ($username ? render_chat_input($_SERVER['PHP_SELF'], $field_size=40) : '');
$channel = 1;

// Take in a chat and record it to the database.
if ($username) {
	if ($command == "postnow" && $message) {
		sendChat($username, $channel, $message);
	}
}

// Output section.

echo render_chat_refresh($not_mini=true); // Write out the js to refresh to refresh page to full chat.

echo "<div id='full-chat'>";

echo $input_form;

echo render_active_members($sql);

echo render_chat_messages($sql, $chatlength);

echo "</div>"; // End of full_chat div.

echo render_footer(); // Don't skip the quickstat.
?>
