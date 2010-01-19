<?php
// For true user-to-user or user-to-clan messages as opposed to events.
function send_message($from_id, $to_id, $msg) {
	global $sql;
	$sql->Insert("INSERT INTO messages (message_id, send_from, send_to, message, date) VALUES (default,'".sql($from_id)."','".sql($to_id)."','".sql($msg)."',now())");
}

function get_messages($to_id, $limit=null, $offset=null) {
    global $sql;

	if (!is_numeric($limit)) {
		$limit = 25;
	}

	if (!is_numeric($offset)) {
		$offset = 0;
	}

	$sql->Query("SELECT send_from, message, unread, uname as from FROM messages join players on send_from = player_id where send_to = '".sql($to_id)."' ORDER BY date DESC limit ".sql($limit)." offset ".sql($offset));

	return $sql->fetchAll();
}

function read_messages($to_id) {
	global $sql;
	$sql->Update("UPDATE messages set unread = 0 where send_to = '".sql($to_id)."'");
}

function delete_messages(){
    global $sql;
    $user_id = get_user_id();
    $sql->Delete("DELETE from messages where send_to = '".sql($user_id)."'");
}

function message_count(){
    $user_id = get_user_id();
    global $sql;
    return $sql->QueryItem("SELECT count(*) from messages where send_to = '".sql($user_id)."'");
}

// Return an array of nav settings.
function render_message_nav($current_page, $pages, $limit){
    $res = '';
    if($pages>1){
        $res .= "<div class='message-nav'>";
        if(($current_page-1)>0){
            $res .= "<a href='messages.php?page=".($current_page-1)."'>Prev</a>";
        } else {
            $res .= "Prev";
        }
        $res .= "- $current_page / $pages -";
        if(($current_page+1)<($pages+1)){
            $res .= "<a href='messages.php?page=".($current_page+1)."'>Next</a>";
        } else {
            $res .= "Next";
        }
        $res .= "</div>";
    }
    return $res;
}

function message_to_clan($p_message) {
	global $sql;

	$error    = null;
	$user_id  = get_user_id();
	$username = get_username();
	$clan_id  = get_clan_by_player_id($user_id)->getID();

	$sql->Query(
	    "SELECT player_id, uname 
	    FROM clan JOIN clan_player ON _clan_id = clan_id JOIN players ON player_id = _player_id 
	    WHERE clan_id = ' $clan_id'");

	$clan_members = $sql->fetchAll();
	$messaged_to = '';
	$comma = '';

	foreach ($clan_members as $loop_member) {
		send_message($user_id, $loop_member['player_id'], "CLAN: ".$p_message);
		$messaged_to .= $comma.$loop_member['uname'];
		$comma = ', ';
	}

	return $messaged_to;
}

?>
