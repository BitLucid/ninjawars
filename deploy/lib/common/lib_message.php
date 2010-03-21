<?php
// For true user-to-user or user-to-clan messages as opposed to events.
function send_message($from_id, $to_id, $msg) {
	DatabaseConnection::getInstance();
	$statement = DatabaseConnection::$pdo->prepare("INSERT INTO messages (message_id, send_from, send_to, message, date) VALUES (default, :from, :to, :message, now())");
	$statement->bindValue(':from', $from_id);
	$statement->bindValue(':to', $to_id);
	$statement->bindValue(':message', $msg);
	$statement->execute();
}

function get_messages($to_id, $limit=null, $offset=null) {
	if (!is_numeric($limit)) {
		$limit = 25;
	}

	if (!is_numeric($offset)) {
		$offset = 0;
	}

	DatabaseConnection::getInstance();
	$statement = DatabaseConnection::$pdo->prepare("SELECT send_from, message, unread, uname AS from FROM messages JOIN players ON send_from = player_id WHERE send_to = :to ORDER BY date DESC LIMIT :limit OFFSET :offset");
	$statement->bindValue(':to', $to_id);
	$statement->bindValue(':limit', $limit);
	$statement->bindValue(':offset', $offset);
	$statement->execute();

	return $statement->fetchAll();
}

function read_messages($to_id) {
	DatabaseConnection::getInstance();
	$statement = DatabaseConnection::$pdo->prepare("UPDATE messages SET unread = 0 WHERE send_to = :to");
	$statement->bindValue(':to', $to_id);
	$statement->execute();
}

function delete_messages() {
	DatabaseConnection::getInstance();

	$user_id = get_user_id();
	$statement = DatabaseConnection::$pdo->prepare("DELETE from messages where send_to = :to");
	$statement->bindValue(':to', $user_id);
	$statement->execute();
}

function message_count() {
	DatabaseConnection::getInstance();

	$user_id = get_user_id();
	$statement = DatabaseConnection::$pdo->prepare("SELECT count(*) from messages where send_to = :to");
	$statement->bindValue(':to', $user_id);
	$statement->execute();
	return $statement->fetchColumn();
}

// Return an array of nav settings.
function render_message_nav($current_page, $pages, $limit) {
	$res = '';

	if ($pages > 1) {
		$res .= "<div class='message-nav'>";

		if (($current_page - 1) > 0) {
			$res .= "<a href='messages.php?page=".($current_page - 1)."'>Prev</a>";
		} else {
			$res .= "Prev";
		}

		$res .= "- $current_page / $pages -";

		if (($current_page + 1) < ($pages + 1)) {
			$res .= "<a href='messages.php?page=".($current_page + 1)."'>Next</a>";
		} else {
			$res .= "Next";
		}

		$res .= "</div>";
	}

	return $res;
}

function message_to_clan($p_message) {
	DatabaseConnection::getInstance();

	$error    = null;
	$user_id  = get_user_id();
	$username = get_username();
	$clan_id  = get_clan_by_player_id($user_id)->getID();

	$statement = DatabaseConnection::$pdo->prepare("SELECT player_id, uname 
	    FROM clan JOIN clan_player ON _clan_id = clan_id JOIN players ON player_id = _player_id 
	    WHERE clan_id = :clan");
	$statement->bindValue(':clan', $clan_id);
	$statement->execute();

	$messaged_to = '';
	$comma = '';

	while ($loop_member = $statement->fetch()) {
		send_message($user_id, $loop_member['player_id'], "CLAN: ".$p_message);
		$messaged_to .= $comma.$loop_member['uname'];
		$comma = ', ';
	}

	return $messaged_to;
}
?>
