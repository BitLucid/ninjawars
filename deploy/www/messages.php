<?php
require_once(CORE.'data/Message.php');

use app\data\Message;

$private    = true;
$alive      = false;

if ($error = init($private, $alive)) {
	display_error($error);
} else {

$to        = in('to'); // The target of the message, if any were specified.
$to = $to? $to : get_setting('last_messaged');
$to_clan   = in('toclan');
$messenger = in('messenger'); // naive spam detection attempt
$message   = in('message', null, null); // Unfiltered input for this message.
$target_id = (int) in('target_id')? (int) in('target_id') : ($to ? get_user_id($to) : null); // Id takes precedence
$ninja = new Player(self_char_id());
$clan      = $ninja->getClan(); //get_clan_by_player_id($user_id);
$has_clan  = (bool)$clan || $ninja->isAdmin();
$has_clan  = ($clan ? true : false);
$page      = in('page', 1, 'non_negative_int');
$limit     = 25;
$offset    = non_negative_int(($page - 1) * $limit);
$delete    = in('delete');

$type_filter = in('type'); // Clan chat or normal messages.
$type_filter = restrict_to($type_filter, array(0, 1));
// Currently there are only the two types of messages, but if types are added in the future...
switch($type_filter){
	case(1):
		$viewed_type = 'clan';
		$current_tab = 'clan';
	break;
	default:
		$viewed_type = 'personal';
		$current_tab = 'messages';
	break;
}


$message_sent_to = null; // Names or name to display.
$message_to = null; // strings clan or individual if sent to those respectively.

if($target_id){
	$to = get_char_name($target_id);	
}
set_setting('last_messaged', $to);

// Sending mail section.
if ($message && $messenger) {
	if ($to_clan && $has_clan) {
		$message_sent_to = message_to_clan($message);
		$message_to = 'clan';
		$type_filter = 1;
	} elseif ((bool)$target_id) {
		send_message($ninja->id(), $target_id, $message);
		$message_sent_to = $to;
		$message_to = 'individual';
	}
}

if ($delete) {
	delete_messages($type_filter);
}

$messages      = get_messages($ninja->id(), $limit, $offset, $type_filter);
$message_count = message_count($type_filter);
$pages         = ceil($message_count / $limit);  // Total pages.
$messages      = $messages->fetchAll();
$focus_clan = $message_to;

$current_page = $page;

read_messages($ninja->id()); // mark messages as read for next viewing.

// TODO: Handle "send" ing to specific, known users.

$individual_or_clan = ($message_to == 'individual' || $message_to == 'clan');
$parts = compact('message_sent_to', 'messages', 'current_tab', 'to', 'has_clan', 
	'type_filter', 'viewed_type', 'individual_or_clan', 'pages', 'current_page');

display_page(
	'messages.tpl'
	, 'Messages'
	, $parts
	, ['quickstat' => false]
);
}

