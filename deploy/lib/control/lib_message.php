<?php
// For true user-to-user or user-to-clan messages, as opposed to game events.
function send_message($from_id, $to_id, $msg, $type=0) {
	$msg = trim($msg);
	
	$type = restrict_to($type, array(0, 1)); // 0 = direct, 1 = clan

	if (strlen($msg) > MAX_MSG_LENGTH) {
		throw new Exception('The message was longer than the maximum message length of '.MAX_MSG_LENGTH.' characters.');
	}

	$prevMsg = query_item("SELECT message FROM messages WHERE send_from = :from AND send_to = :to ORDER BY date DESC LIMIT 1"
		, array(
			':from' => $from_id
			, ':to' => $to_id
		)
	);

	if ($prevMsg != $msg) {
		query("INSERT INTO messages (message_id, send_from, send_to, message, type, date) VALUES (default, :from, :to, :message, :type, now())"
			, array(
				':from'      => $from_id
				, ':to'      => $to_id
				, ':message' => $msg
				, ':type'    => $type
			)
		);

		return true;
	} else {
		return false;
	}
}

// Get either direct messages or clan messages.
function get_messages($to_id, $limit=25, $offset=0, $filter=0) {
	if (!is_numeric($limit)) {
		$limit = 25;
	}

	if (!is_numeric($offset)) {
		$offset = 0;
	}
	
	
	// Filters only non-clan messages based on the type = 0, which is the default.
	$res = query("SELECT send_from, message, unread, uname AS from FROM messages JOIN players ON send_from = player_id WHERE send_to = :to and type = :filter ORDER BY date DESC LIMIT :limit OFFSET :offset"
		, array(
			':to'       => $to_id
			, ':limit'  => $limit
			, ':offset' => $offset
			, ':filter' => $filter
		)
	);

	return $res;
}

function read_messages($to_id) {
	query("UPDATE messages SET unread = 0 WHERE send_to = :to", array(':to'=>$to_id));
}

function delete_messages($filter_type=0) {
	$user_id = get_user_id();
	query("DELETE from messages where send_to = :to and type = :type", array(':to'=>array($user_id, PDO::PARAM_INT), ':type'=>$filter_type));
}

function message_count() {
	return query_item("SELECT count(*) from messages where send_to = :to", array(':to'=>get_user_id()));
}

function unread_message_count() {
	return query_item("SELECT count(*) from messages where send_to = :to and unread != 0", array(':to'=>get_user_id()));
}

// Send a message to the clan members.
function message_to_clan($p_message) {
	$error    = null;
	$user_id  = get_user_id();
	$username = get_username();
	$clan_id  = get_clan_by_player_id($user_id)->getID();

	$clan_members = query_resultset("SELECT player_id, uname
	    FROM clan JOIN clan_player ON _clan_id = clan_id JOIN players ON player_id = _player_id
	    WHERE clan_id = :clan"
		, array(':clan'=> $clan_id)
	);

	$messaged_to = array();

	foreach ($clan_members as $loop_member) {
		send_message($user_id, $loop_member['player_id'], $p_message, $type=1);
		$messaged_to[] = $loop_member['uname'];
	}

	return implode(', ', $messaged_to);
}
?>
