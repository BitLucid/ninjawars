<?php
$private    = true;
$alive      = false;
$quickstat  = false;
$page_title = "Messages";
include SERVER_ROOT."interface/header.php";
require_once(LIB_ROOT."specific/lib_mail.php");

// TODO: Check up on message limits.
// TODO: Turn clan mail into a self postback instead of going to mail_send.


$user_id = get_user_id();
$username = get_username();
$messages = get_messages($user_id);
$clan_send = false;

if (getClan($username) != ""){
    $clan_send = true;
}

read_messages($user_id); // mark messages as read for next viewing.

// TODO: Handle "send" and "deletion";
$message_list = '';
if(!empty($messages)){
    foreach($messages as $loop_message){
        $message_list .= render_template('single_message.tpl', array('message' => $loop_message));
    }
}

$parts = get_certain_vars(get_defined_vars());

echo render_template('messages.tpl', $parts);


include SERVER_ROOT."interface/footer.php";

?>
