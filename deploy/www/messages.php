<?php
$private    = true;
$alive      = false;

if ($error = init($private, $alive)) {
	display_error($error);
} else {

$to        = in('to'); // The target of the message, if any were specified.
$to = $to? $to : get_setting('last_messaged');
set_setting('last_messaged', $to);
$to_clan   = in('toclan');
$messenger = in('messenger'); // naive spam detection attempt
$message   = in('message', null, null); // Unfiltered input for this message.
$target_id = ($to ? get_user_id($to) : in('target_id'));
$user_id   = get_user_id();
$username  = get_username();
$clan      = get_clan_by_player_id($user_id);
$has_clan  = ($clan ? true : false);
$page      = in('page', 1, 'non_negative_int');
$limit     = 25;
$offset    = non_negative_int(($page - 1) * $limit);
$delete    = in('delete');

$type_filter = in('type'); // Clan chat or normal messages.
$type_filter = restrict_to($type_filter, array(0, 1));


$message_sent_to = null; // Names or name to display.
$message_to = null; // strings clan or individual if sent to those respectively.

if($target_id && !$to){
	$to = get_char_name($target_id);	
}

// Sending mail section.
if ($message && $messenger) {
	if ($to_clan && $has_clan) {
		$message_sent_to = message_to_clan($message);
		$message_to = 'clan';
		$type_filter = 1;
	} elseif (!!$target_id) {
		send_message($user_id, $target_id, $message);
		$message_sent_to = $to;
		$message_to = 'individual';
	}
}

//debug($type_filter, $message_to);

$viewed_type = $type_filter === 0? 'individual' : 'clan'; // Viewed message type.
$current_tab = $type_filter === 0? 'messages' : 'clan'; // Current tab.

if ($delete) {
	delete_messages($type_filter);
}

$messages      = get_messages($user_id, $limit, $offset, $type_filter);
$message_count = message_count($type_filter);
$pages         = ceil($message_count / $limit);  // Total pages.
$messages      = $messages->fetchAll();

$current_page = $page;

read_messages($user_id); // mark messages as read for next viewing.

// TODO: Handle "send" ing to specific, known users.

$parts = get_certain_vars(get_defined_vars(), array('messages'));

display_page(
	'messages.tpl'
	, 'Messages'
	, $parts
	, array(
		'quickstat' => false
	)
);
}
?>
