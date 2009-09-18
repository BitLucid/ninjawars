<?php
// For true user-to-user or user-to-clan messages as opposed to events.
function send_user_message($from_id,$to_id,$msg) {
  global $sql;
  $sql->Insert("INSERT INTO messages (message_id, send_from, send_to, message, date) VALUES (default,".sql($from_id).",".sql($to_id).",'".sql($msg)."',now())");
}

function get_messages($to_id){
    global $sql;
    $sql->Query("SELECT send_from, message, unread, uname as from FROM messages join players on send_from = player_id where send_to = '".sql($to_id)."' ORDER BY date DESC");
    return $sql->fetchAll();
}

function read_messages($to_id){
    global $sql;
    $sql->Update("UPDATE messages set unread = 0 where send_to = '".sql($to_id)."'");
}

/* TODO: Create send to clan function 
function message_to_clan($message){
    global $sql;
    $user_id = get_user_id();
    $sql->Query("SELECT uname, player_id from players where 
    
}*/

?>
