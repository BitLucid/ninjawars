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

