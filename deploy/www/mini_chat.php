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
$chatmax       = 800;

// Take in a chat and record it to the database.
if ($user_id) {
	if ($command == "postnow" && $message) {
		send_chat($user_id, $message);
	}
}

$stats          = membership_and_combat_stats();
$player_count   = $stats['player_count'];
$players_online = $stats['players_online'];
$active_chars   = $stats['active_chars'];

// Active ninja.
$members = first_value($active_chars, '0');

// Online ninja.
$membersTotal = whichever($players_online, '0');

// Total ninja.
$total_chars = whichever($player_count, '0');

// Output section.

$chat_messages = render_chat_messages(min($chatlength, $chatmax), true);

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
