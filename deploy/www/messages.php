<?php
require_once(CORE.'data/Message.php');
require_once(CORE.'data/ClanFactory.php');

use app\data\Message;

$private    = true;
$alive      = false;

if ($error = init($private, $alive)) {
	display_error($error);
} else {

$command = in('command');
$to        = in('to'); // The target of the message, if any were specified.
$to = $to? $to : get_setting('last_messaged');
$to_clan   = in('toclan');
$messenger = in('messenger'); // naive spam detection attempt
$message   = in('message', null, null); // Unfiltered input for this message.
$target_id = (int) in('target_id')? (int) in('target_id') : ($to ? get_user_id($to) : null); // Id takes precedence
$ninja = new Player(self_char_id());
$clan      = ClanFactory::clanOfMember($ninja);
$has_clan  = ($clan ? true : false);
$page      = in('page', 1, 'non_negative_int');
$limit     = 25;
$offset    = non_negative_int(($page - 1) * $limit);
$delete    = in('delete');

$type = in('type'); // Clan chat or normal messages.
$type = restrict_to($type, array(0, 1));

// Currently there are only the two types of messages.
switch(true){
	case($command == 'clan' && $_POST):
		$messages_type = 'clan';
		$current_tab = 'clan';
		$type = 1;
	break;
	case($command == 'clan'):
		$messages_type = 'clan';
		$current_tab = 'clan';
		$type = 1;
	break;
	case($command == 'personal' && $_POST):
		// Try to send
		$messages_type = 'personal';
		$current_tab = 'messages';
		$type = 0;
	break;
	default:
		$command = 'personal';
		$messages_type = 'personal';
		$current_tab = 'messages';
		$type = 0;
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
		$type = 1;
	} elseif ((bool)$target_id) {
		send_message($ninja->id(), $target_id, $message);
		$message_sent_to = $to;
		$message_to = 'individual';
		$type = 0;
	}
}

if ($delete) {
	Message::deleteByReceiver($ninja, $type);
}

$messages = Message::findByReceiver($ninja, $type, $limit, $offset);

//$messages      = Message::finget_messages($ninja->id(), $limit, $offset, $type);
$message_count = Message::countByReceiver($ninja, $type); // To count all the messages
$pages         = ceil($message_count / $limit);  // Total pages.

$current_page = $page;

Message::markAsRead($ninja, $type); // mark messages as read for next viewing.

// TODO: Handle "send" ing to specific, known users.

$individual_or_clan = ($message_to == 'individual' || $message_to == 'clan');
$parts = compact('command', 'message_sent_to', 'messages', 'current_tab', 'to', 'has_clan', 
	'type_filter', 'messages_type', 'individual_or_clan', 'pages', 'current_page', 'message_to');

display_page(
	'messages.tpl'
	, 'Messages'
	, $parts
	, ['quickstat' => false]
);

}

