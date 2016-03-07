<?php
use NinjaWars\core\data\DatabaseConnection;

function send_chat($user_id, $msg) {
	DatabaseConnection::getInstance();

	$msg = trim($msg);

	if ($msg) {
		$statement = DatabaseConnection::$pdo->prepare("SELECT message FROM chat WHERE sender_id = :sender ORDER BY date DESC LIMIT 1");
		$statement->bindValue(':sender', $user_id);
		$statement->execute();
		$prevMsg = trim($statement->fetchColumn());

		if ($prevMsg != $msg) {
			$statement = DatabaseConnection::$pdo->prepare("INSERT INTO chat (chat_id, sender_id, message, date) VALUES (default, :sender, :message, now())");
			$statement->bindValue(':sender', $user_id);
			$statement->bindValue(':message', $msg);
			$statement->execute();

			return true;
		}
	}

	return false;

	// could add channels later.
}
