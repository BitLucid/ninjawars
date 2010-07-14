<?php
$private = false;
$alive   = false;

if ($error = init($private, $alive)) {
	display_error($error);
} else {

require_once(LIB_ROOT."specific/lib_chat.php"); // Require all the chat helper and rendering functions.

$default_limit = 360;
$field_size    = 40;
$chatlength    = in('chatlength', $default_limit, 'toInt');
$message       = in('message', null, 'no filter'); // Essentially no filtering.
$command       = in('command');
$sentMessage   = in('message');
$sent          = false;
$user_id       = get_user_id();
$target        = $_SERVER['PHP_SELF'];
$channel       = 1;

// Take in a chat and record it to the database.
if ($user_id) {
	if ($command == "postnow" && $message) {
		send_chat($user_id, $message);
	}
}

// Output section.

$stats        = membership_and_combat_stats();
$total_chars  = $stats['player_count'];
$chars_online = $stats['players_online'];
$active_chars = $stats['active_chars'];

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
