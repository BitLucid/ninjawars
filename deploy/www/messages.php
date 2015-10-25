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
$informational = in('informational');

$type = in('type'); // Clan chat or normal messages.
$type = restrict_to($type, array(0, 1));

$message_sent_to = null; // Names or name to display.
$message_to = null; // strings clan or individual if sent to those respectively.

if($target_id){
	$to = get_char_name($target_id);	
}
set_setting('last_messaged', $to);


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
	case($command == 'delete' && $_POST):
		if ($delete) {
			Message::deleteByReceiver($ninja, $type);
			$type = in('type');
			if($type == 1){
				$command = 'clan';
			} else {
				$command = 'personal';
			}
			redirect('/messages.php?command='.$command.'&informational='.url('Messages deleted'));
		}
	break;
	default:
		$command = 'personal';
		$messages_type = 'personal';
		$current_tab = 'messages';
		$type = 0;
}

// Sending mail section.
if ($message && $messenger) {
	if ($to_clan && $has_clan) {
		$type = 1;
		$target_id_list = $clan->getMemberIds();
		$passfail = Message::sendToGroup($ninja, $target_id_list, $message, $type);
		$message_sent_to = 'your clan';
		$message_to = 'clan';
	} elseif ((bool)$target_id) {
		Message::create(['send_from'=>$ninja->id(), 'send_to'=>$target_id, 'message'=>$message, 'type'=>$type]);
		$message_sent_to = $to;
		$message_to = 'individual';
		$type = 0;
	}
}

// An illuminate collection object.
$messages = Message::findByReceiver($ninja, $type, $limit, $offset);

$message_count = Message::countByReceiver($ninja, $type); // To count all the messages
$pages         = ceil($message_count / $limit);  // Total pages.
$current_page = $page;

Message::markAsRead($ninja, $type); // mark messages as read for next viewing.

// TODO: Handle "send" ing to specific, known users.

$individual_or_clan = ($message_to == 'individual' || $message_to == 'clan');
$parts = compact('command', 'message_sent_to', 'messages', 'current_tab', 'to', 'has_clan', 
	'type_filter', 'type', 'messages_type', 'individual_or_clan', 'pages', 'current_page', 'message_to', 'informational');

display_page(
	'messages.tpl'
	, 'Messages'
	, $parts
	, ['quickstat' => false]
);

}

