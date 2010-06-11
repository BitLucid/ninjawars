<?php
$private = false;
$alive   = false;

if ($error = init($private, $alive)) {
	display_error($error);
} else {

require_once(LIB_ROOT."specific/lib_chat.php"); // Require all the chat helper and rendering functions.

$default_limit = 360;
$chatlength    = in('chatlength', $default_limit, 'toInt');
$message       = in('message', null, 'no filter'); // Essentially no filtering.
$command       = in('command');
$sentMessage   = in('message');
$sent          = false;
$username      = get_username();
$user_id       = get_user_id();



$input_form    = ($user_id ? render_chat_input($self, $field_size = 40) : ''); // Display chat box if logged in.
$channel       = 1;

// Take in a chat and record it to the database.
if ($user_id) {
	if ($command == "postnow" && $message) {
		send_chat($user_id, $message);
	}
}

// Output section.

$chat_refresh = render_chat_refresh($not_mini=true); // Write out the js to refresh to refresh page to full chat.

$active_members = render_active_members();

$chat_messages = render_chat_messages($chatlength);

$parts = get_certain_vars(get_defined_vars(), array());

display_page(
	'village.tpl'
	, 'Chat Board'
	, $parts
	, array (
		'quickstat' => false
	)
);
}
?>
