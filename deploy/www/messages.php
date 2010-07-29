<?php
$private    = true;
$alive      = false;

if ($error = init($private, $alive)) {
	display_error($error);
} else {

$to        = in('to'); // The target of the message, if any were specified.
$to_clan   = in('toclan');
$messenger = in('messenger'); // naive spam detection attempt
$message   = in('message', null, null); // Unfiltered input for this message.
$target_id = ($to ? get_user_id($to) : null);
$user_id   = get_user_id();
$username  = get_username();
$clan      = get_clan_by_player_id($user_id);
$has_clan  = ($clan ? true : false);
$page      = in('page', 1, 'toInt');
$limit     = 25;
$offset    = (($page - 1) * $limit);
$delete    = in('delete');

$message_sent_to = null;

// Sending mail section.
if ($message && $messenger) {
	if ($to_clan && $has_clan) {
		$message_sent_to = message_to_clan($message);
	} elseif (!!$target_id) {
		send_message($user_id, $target_id, $message);
		$message_sent_to = $to;
	}
}

if ($delete) {
	delete_messages();
}

$messages      = get_messages($user_id, $limit, $offset);
$message_count = message_count();
$pages         = ceil($message_count / $limit);  // Total pages.

$current_page = $page;

read_messages($user_id); // mark messages as read for next viewing.

// TODO: Handle "send" ing to specific, known users.
$message_list = '';
foreach ($messages as $loop_message) {
	$loop_message['message'] = out($loop_message['message']);
	$message_list .= render_template('single_message.tpl', array('message' => $loop_message));
}

$parts = get_certain_vars(get_defined_vars());

display_page(
	'messages.tpl'
	, 'Messages'
	, $parts
);
}
?>
