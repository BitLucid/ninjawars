<?php
// TODO: Only allow for ajax requests.
// TODO: Turn the data fecthing into an associative fetch instead of DB_BOTH type.

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
function render_json($type, $jsoncallback) {
	$valid_type_map = array('player'=>'json_player','latest_event'=>'json_latest_event', 'chats'=>'json_chats', 'latest_message'=>'json_latest_message', 'index'=>'json_index', 'latest_chat_id'=>'json_latest_chat_id');
	$res = null;

	if (isset($valid_type_map[$type])) {
		$res = $jsoncallback.'('.$valid_type_map[$type]().')';   
	}

	return $res;
}

function json_latest_message() {
	DatabaseConnection::getInstance();
	$user_id = (int) get_user_id();

	$statement = DatabaseConnection::$pdo->prepare("SELECT message_id, message, date, send_to, send_from, unread, uname AS sender FROM messages JOIN players ON player_id = send_from WHERE send_to = :userID1 AND send_from != :userID2 ORDER BY date DESC LIMIT 1");
	$statement->bindValue(':userID1', $user_id);
	$statement->bindValue(':userID2', $user_id);
	$statement->execute();

	// Skips message sent by self, i.e. clan send messages.
	return '{"message":'.json_encode($statement->fetch()).'}';
}

function json_latest_event() {
	DatabaseConnection::getInstance();
	$user_id = (int) get_user_id();

	$statement = DatabaseConnection::$pdo->prepare("SELECT event_id, message AS event, date, send_to, send_from, unread, uname AS sender FROM events JOIN players ON player_id = send_from WHERE send_to = :userID ORDER BY date DESC LIMIT 1");
	$statement->bindValue(':userID', $user_id);
	$statement->execute();

	return '{"event":'.json_encode($statement->fetch()).'}';
}

function json_player() {
	$player = get_player_info();
	return '{"player":'.json_encode($player).'}';
}

function json_chats() {
	DatabaseConnection::getInstance();
	$statement = DatabaseConnection::$pdo->query("SELECT * FROM chat ORDER BY date DESC");
	$chats = $statement->fetchAll();

	return '{"chats":'.json_encode($chats).'}';
}

function json_latest_chat_id() {
	DatabaseConnection::getInstance();
	$statement = DatabaseConnection::$pdo->query("SELECT chat_id FROM chat ORDER BY date DESC limit 1");

	return '{"latest_chat_id":'.json_encode($statement->fetch()).'}';
}

function json_index() {
	DatabaseConnection::getInstance();
	$player   = get_player_info();
	$events   = array();
	$messages = array();
	$user_id  = $player['player_id'];

	if ($user_id) {
		$events = DatabaseConnection::$pdo->prepare("SELECT event_id, message AS event, date, send_to, send_from, unread, uname AS sender FROM events JOIN players ON player_id = send_from WHERE send_to = :userID ORDER BY date DESC LIMIT 1");
		$events->bindValue(':userID', $user_id);

		$events->execute();

		$messages = DatabaseConnection::$pdo->prepare("SELECT message_id, message, date, send_to, send_from, unread, uname AS sender FROM messages JOIN players ON player_id = send_from WHERE send_to = :userID1 AND send_from != :userID2 ORDER BY date DESC LIMIT 1");
		$messages->bindValue(':userID1', $user_id);
		$messages->bindValue(':userID2', $user_id);

		$messages->execute();
	}

	return '{"player":'.json_encode($player).',"message":'.json_encode($messages->fetch()).',"event":'.json_encode($events->fetch()).'}';
}
?>
