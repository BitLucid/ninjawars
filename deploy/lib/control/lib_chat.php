<?php
/*
 * Chat helper functions.
 *
 * @package messages
 * @subpackage chat
 */


// ************************************
// ******** CHAT FUNCTIONS ************
// ************************************

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

// Get all the chat messages info.
function get_chats($chatlength) {
	$limit = ($chatlength ? 'LIMIT :limit' : '');
	$bindings = array();
	if ($limit) {
	    $bindings[':limit'] = $chatlength;
	}

	$chats = query_resultset("SELECT sender_id, uname, message, date, age(now(), date) AS ago FROM chat
        JOIN players ON chat.sender_id = player_id ORDER BY chat_id DESC ".$limit, $bindings);

	return $chats;
}

// Total number of chats available.
function get_chat_count() {
	return query_item("SELECT count(*) FROM chat");
}

// parse the date/time for the chat.
function time_ago($time, $previous) {
	// Need to modify the database calls in order to retrieve the right ago message stuff.
	$time_array = time_to_array($time);
	$similar = false;
	$res = '';

	if ($previous) {
		$previous_array = time_to_array($previous);
		/* If the time is substantially different from the previous (1 minute or more),
		then mark down how long ago the time was and return it (to be displayed after the chat
		If the minutes, hours, and days are similar between two messages, they're similar.*/

		if ($time_array['minutes'] == $previous_array['minutes']
			&& $time_array['hours'] == $previous_array['hours']
			&& (
				(!isset($time_array['days']) || !isset($previous_array['days']))
				|| $time_array['days'] == $previous_array['days'])) {
			// So no need to change the ago message.
			$similar = true;
		}
	}

	if (!$similar) { // Display time if no previous or non-similar previous time.
		$res = ago_string($time_array);
	}

	return $res;
}

// Transform the time format into an array of its different parts.
function time_to_array($time) {
	$returnValue = array();

	$divider = strrpos($time, ' ');

	if ($divider) {
		$time_only = substr($time, $divider+1);
		$date_only = substr($time, 0, $divider);

		$date_array = explode(' ', $date_only);

		foreach ($date_array AS $index=>$value) {
			if (stripos($value, 'day') !== false) {
				$returnValue['days'] = $date_array[--$index];
				break;
			}
		}
	} else {
		$time_only = $time;
		$returnValue['days'] = 0;
	}

	$time_array = preg_split("/\D/", $time_only); // Split on non-digits (\D).

	$returnValue['hours']       = $time_array[0];
	$returnValue['minutes']     = $time_array[1];
	$returnValue['seconds']     = $time_array[2];
	$returnValue['nanoseconds'] = $time_array[3];

	return $returnValue;
}

// Format the string of the amount of time that it was ago.
function ago_string($time_array) {
	if (@$time_array['days'] > 0) {
		$res = (int)$time_array['days'].(1 == (int)$time_array['days'] ? ' day' : ' days');
	} elseif ($time_array['hours'] > 0) {
		$res = (int)$time_array['hours'].(1 == (int)$time_array['hours'] ? ' hour' : ' hours');
	} else {
		$res = (int)$time_array['minutes'].(1 == (int)$time_array['minutes'] ? ' minute' : ' minutes');
	}

	return '('.$res.' ago)';
}
?>
