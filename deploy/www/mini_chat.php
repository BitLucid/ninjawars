<?php
$private = false;
$alive   = false;

if ($error = init($private, $alive)) {
	display_error($error);
} else {

require_once(LIB_ROOT."specific/lib_chat.php"); // Require all the chat helper and rendering functions.

$default_limit = 20;
$chatlength    = in('chatlength', $default_limit, 'toInt');
$message       = in('message', null, 'no filter'); // Essentially no filtering.
$command       = in('command');
$sentMessage   = in('message');
$chat_submit   = in('chat_submit');
$sent          = false;
$user_id       = get_user_id();

// Take in a chat and record it to the database.
if ($user_id) {
	if ($command == "postnow" && $message) {
		send_chat($user_id, $message);
	}
}

$members = whichever(query_item("SELECT count(*) FROM ppl_online WHERE member = true AND activity > (now() - CAST('15 minutes' AS interval))"), '0');

$membersTotal = whichever(query_item("SELECT count(*) FROM ppl_online WHERE member = true"), '0');

$total_chars = whichever(query_item("SELECT count(*) FROM players where confirmed = 1"), '0');

// Output section.

$chat_messages = render_chat_messages($chatlength, true);

display_page(
	'mini_chat.tpl'	// *** Main template ***
	, 'Mini Chat' // *** Page Title ***
	, get_certain_vars(get_defined_vars(), array()) // *** Page Variables ***
	, array( // *** Page Options ***
		'quickstat' => false
	)
);
}
?>
