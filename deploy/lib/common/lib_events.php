<?php
// Now only a wrapper for the send_event function. 
function sendMessage($from, $to, $msg, $filter=false) {
	// Filter argument is deprecated now.
	$from_id = (int) get_user_id($from);
	$to_id = get_user_id($to);
	send_event($from_id, $to_id, $msg);
}

// For true user-to-user or user-to-clan messages as opposed to events.
function send_event($from_id, $to_id, $msg) {
	global $sql;
	if (!$to_id) {
		$to_id = get_user_id();
	}

	if (!is_numeric($from_id) || !is_numeric($to_id)) {
		throw new Exception('A player id wasn\'t sent in to the send_event function.');
	}

	$sql->Insert("INSERT INTO events (event_id, send_from, send_to, message, date) 
    VALUES 
    (default, '".sql($from_id)."','".sql($to_id)."','".sql($msg)."',now())");
}

function get_events($user_id, $limit=null) {
	global $sql;
	$sql->Query("SELECT send_from, message, unread, uname as from FROM events 
        join players on send_from = player_id where send_to = '".sql($user_id)."' ORDER BY date DESC ".($limit? "limit $limit" : '')."");
	return $sql->fetchAll();
}

function read_events($user_id) {
	global $sql;
	$sql->Update("UPDATE events set unread = 0 where send_to = '".sql($user_id)."'");
}


?>
