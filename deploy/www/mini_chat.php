<?php
require_once(LIB_ROOT."specific/lib_chat.php"); // Require all the chat helper and rendering functions.

init(); // Initialize the environment.

$default_limit = 20;
$chatlength = in('chatlength', $default_limit, 'toInt');
$message = in('message', null, 'forChat'); // Essentially no filtering.
$command = in('command');
$sentMessage = in('message');
$chat_submit = in('chat_submit');
$sent = false;

$user_id = get_user_id();

// Take in a chat and record it to the database.
if ($user_id) {
	if ($command == "postnow" && $message) {
		send_chat($user_id, $message);
	}
}

$sql = new DBAccess();
$members = $sql->QueryItem(
        "select count(*) from ppl_online where member = true AND activity > (now() - CAST('30 minutes' AS interval))");
$members = either($members, '0');
$membersTotal = $sql->QueryItem("select count(*) from ppl_online where member = true");
$membersTotal = either($membersTotal, '0');


// Output section.

$chat_messages = render_chat_messages($chatlength, true);

// $template, $title=null, $local_vars=array(), $options=null

echo render_page('mini_chat.tpl', 'Mini Chat', get_certain_vars(get_defined_vars(), array()), $options=array(
        'skip_quickstat'=>true,
        'alive'=>false,
        'private'=>false,
        'quickstat'=>null,
    ));



?>
