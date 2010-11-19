<?php
$private = false;
$alive   = false;

if ($error = init($private, $alive)) {
	display_error($error);
} else {

require_once(LIB_ROOT."control/lib_chat.php"); // Require all the chat helper and rendering functions.

$default_limit = 20;
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

$chats = get_chats($chatlength);
$chats = $chats->fetchAll();
$message_count = get_chat_count();

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

$template->register_function('time_ago', 'get_time_ago');

display_prepped_template($template);

}
?>
