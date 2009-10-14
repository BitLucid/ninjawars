<?php
// TODO: Only allow for ajax requests.
// TODO: Turn the DBAccess into an associative fetch instead of DB_BOTH type.

// Check login to allow for information.
// Recent mails.
// Recent events.
// Check chats.
$type = in('type', null);
$jsoncallback = in('jsoncallback');
echo render_json($type, $jsoncallback);

// Make sure to default to private, just as a security reminder.


/**
 * Determine which function to call to get the json for.
**/
function render_json($type, $jsoncallback){
    $valid_type_map = array('player'=>'json_player','latest_event'=>'json_latest_event', 'chats'=>'json_chats', 'latest_message'=>'json_latest_message');
    $res = null;
    if($valid_type_map[$type]){
        $res = $jsoncallback.'('.$valid_type_map[$type]().')';   
    }
    return $res;
}

function json_latest_message(){
    $sql = new DBAccess();
    $user_id = (int) get_user_id();
    $messages = $sql->FetchAll("select message_id, message, date, send_to, send_from, unread, uname as sender from messages join players on player_id = send_from where send_to = '".sql($user_id)."' and send_from != '".sql($user_id)."' order by date desc limit 1");
    // Skips message sent by self, i.e. clan send messages.
    return '{"message":'.json_encode($messages).'}';
}

function json_latest_event(){
    $sql = new DBAccess();
    $user_id = (int) get_user_id();
    $events = $sql->FetchAll("select event_id, message as event, date, send_to, send_from, unread, uname as sender from events join players on player_id = send_from where send_to = '".sql($user_id)."' order by date desc limit 1");
    return '{"event":'.json_encode($events).'}';
}

function json_player(){
    $player = get_player_info();
    return '{"player":'.json_encode($player).'}';
}

function json_chats(){
    $sql = new DBAccess();
    $chats = $sql->FetchAll("select * from chat order by time desc");
    return '{"chats":'.json_encode($chats).'}';
}


?>
