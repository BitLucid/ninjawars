<?php
$private    = true;
$alive      = false;
$quickstat  = false;
$page_title = "Mail";
include SERVER_ROOT."interface/header.php";
require_once(LIB_ROOT."specific/lib_mail.php");

$user_id = get_user_id();

$messages = get_messages($user_id);

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
