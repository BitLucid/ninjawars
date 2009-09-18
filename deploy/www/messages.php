<?php
$private    = true;
$alive      = false;
$quickstat  = false;
$page_title = "Mail";
include SERVER_ROOT."interface/header.php";
require_once(LIB_ROOT."specific/lib_mail.php");

$user_id = get_user_id();

read_messages($user_id); // mark messages as read.

$messages = get_messages($user_id);
// Handle "read" and "deletion".
$message_list = '';
if(!empty($messages)){
    foreach($messages as $message){
        $message_list .= render_template('single_message.tpl', $messages);
    }
}

$parts = get_certain_vars(get_defined_vars());

echo render_template('messages.tpl', $parts);


include SERVER_ROOT."interface/footer.php";

?>
