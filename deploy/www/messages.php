<?php
$private    = true;
$alive      = false;
$quickstat  = false;
$page_title = "Messages";
include SERVER_ROOT."interface/header.php";
require_once(LIB_ROOT."specific/lib_mail.php");

// TODO: Check up on message limits.
// TODO: Turn clan mail into a self postback instead of going to mail_send.

$to = in('to'); // The target of the message, if any were specified.
$to_clan = in('toclan');
$messenger = in('messenger'); // naive spam detection attempt
$message   = in('message', null, 'toMessage'); // Special filtering to a message.
$target_id = $to? get_user_id($to) : null;
$user_id = get_user_id();
$username = get_username();
$clan = getClan($username);
$has_clan = $clan? true : false;

$message_sent_to = null;

// Sending mail section.
if($message && $messenger){
    if($to_clan && $has_clan){
        $message_sent_to = message_to_clan($message);
    } elseif (!!$target_id){
        send_message($user_id, $target_id, $message);
        $message_sent_to = $to; // (
    }
}

$messages = get_messages($user_id);

read_messages($user_id); // mark messages as read for next viewing.

// TODO: Handle "send" and "deletion";
$message_list = '';
if(!empty($messages)){
    foreach($messages as $loop_message){
        $loop_message['message'] = out($loop_message['message']);
        $message_list .= render_template('single_message.tpl', array('message' => $loop_message));
    }
}

$parts = get_certain_vars(get_defined_vars());

echo render_template('messages.tpl', $parts);


include SERVER_ROOT."interface/footer.php";

?>
