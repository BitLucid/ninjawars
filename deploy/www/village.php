<?php
$private = false;
$alive   = false;

if ($error = init($private, $alive)) {
	display_error($error);
} else {

require_once(LIB_ROOT."control/lib_chat.php"); // Require all the chat helper and rendering functions.

$default_limit = 200;
$field_size    = 40;
$chatlength    = in('chatlength', $default_limit, 'toInt');
$message       = in('message', null, 'no filter'); // Essentially no filtering.
$view_all      = in('view_all');
$command       = in('command');
$sentMessage   = in('message');
$sent          = false;
$user_id       = self_char_id();
$target        = $_SERVER['PHP_SELF'];
$channel       = 1;

$chatlength = min(3000, max(30, $chatlength)); // Min 30, max 3000

// Take in a chat and record it to the database.
if ($user_id) {
	if ($command == "postnow" && $message) {
		send_chat($user_id, $message);
		redirect('/village.php');
	}
}

// Output section.

$message_count = get_chat_count();
$chats = get_chats(($view_all? null : $chatlength)); // Limit by chatlength unless a request to view all came in.
$chats = $chats->fetchAll();
$more_chats_to_see = (count($chats)<$message_count? true : null);

$parts = get_certain_vars(get_defined_vars(), array('chats'));

function get_time_ago($p_params, &$tpl) {
	return time_ago($p_params['ago'], $p_params['previous_date']);
}

$template = prep_page(
	'village.tpl'
	, 'Chat Board'
	, $parts
	, array (
		'quickstat' => false
	)
);

//$template->register_function('time_ago', 'get_time_ago');
$template->registerPlugin("function","time_ago", "get_time_ago");

$template->fullDisplay();



} // End of player-is-live so no error block

