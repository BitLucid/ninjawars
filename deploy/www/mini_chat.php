<?php
require_once(LIB_ROOT."specific/lib_chat.php"); // Require all the chat helper and rendering functions.

$private    = false;
$alive      = false;
$page_title = "Mini Chat";
$quickstat  = false;

include SERVER_ROOT."interface/header.php";


$default_limit = 22;
$chatlength = in('chatlength', $default_limit, 'toInt');
$filteredMessage = in('message', null, 'forChat'); // *** Sanitize any message
$command = in('command');
$sentMessage = in('message');
$sent = false;
$username = get_username();
$input_form = ($username ? render_chat_input() : '');

// Take in a chat and record it to the database.
if ($username) {
	if ($command == "postnow" && $filteredMessage) {
		sendChat($username, 'ChatMsg', $filteredMessage); // ChatMsg is deprecated.
	}
}

// Output section.

echo render_chat_refresh(); // Write out the js to refresh.

echo "<div id='mini-chat'>";

echo $input_form;

echo render_active_members($sql);

echo render_chat_messages($sql, $chatlength);

echo "</div>"; // End of mini_chat div.

echo render_footer($quickstat=null, $skip_quickstat=true);
?>
