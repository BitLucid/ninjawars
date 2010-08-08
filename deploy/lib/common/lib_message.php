<?php
// For true user-to-user or user-to-clan messages, 255 chars or less,  as opposed to game events.
function send_message($from_id, $to_id, $msg) {
	$msg = trim($msg);

	$length = strlen($msg);

	$prevMsg = trim(query_item("SELECT message FROM messages WHERE send_from = :from AND send_to = :to ORDER BY date DESC LIMIT 1",
		array(':from'=>$from_id,
		':to'=>$to_id)));

	if ($prevMsg != $msg) {
		query("INSERT INTO messages (message_id, send_from, send_to, message, date) VALUES (default, :from, :to, :message, now())",
			array(':from'=> $from_id,
			':to'=>$to_id,
			':message'=>$msg));
		return true;
	} else {
		return false;
	}
}

function get_messages($to_id, $limit=null, $offset=null) {
	if (!is_numeric($limit)) {
		$limit = 25;
	}

	if (!is_numeric($offset)) {
		$offset = 0;
	}
	
	$res = query("SELECT send_from, message, unread, uname AS from FROM messages JOIN players ON send_from = player_id WHERE send_to = :to ORDER BY date DESC LIMIT :limit OFFSET :offset",
	  array(':to'=>$to_id,
	        ':limit'=>$limit,
	        ':offset'=>$offset));
	return $res;
}

function read_messages($to_id) {
	query("UPDATE messages SET unread = 0 WHERE send_to = :to", array(':to'=>$to_id));
}

function delete_messages() {
	$user_id = get_user_id();
	query("DELETE from messages where send_to = :to", array(':to'=>array($user_id, PDO::PARAM_INT)));
}

function message_count() {
	return query_item("SELECT count(*) from messages where send_to = :to", array(':to'=>get_user_id()));
}

function message_to_clan($p_message) {

	$error    = null;
	$user_id  = get_user_id();
	$username = get_username();
	$clan_id  = get_clan_by_player_id($user_id)->getID();

	$clan_members = query_resultset("SELECT player_id, uname 
	    FROM clan JOIN clan_player ON _clan_id = clan_id JOIN players ON player_id = _player_id 
	    WHERE clan_id = :clan",
	    array(':clan'=> $clan_id));

	$messaged_to = '';
	$comma = '';

	foreach($clan_members as $loop_member){
		send_message($user_id, $loop_member['player_id'], "CLAN: ".$p_message);
		$messaged_to .= $comma.$loop_member['uname'];
		$comma = ', ';
	}

	return $messaged_to;
}
?>
