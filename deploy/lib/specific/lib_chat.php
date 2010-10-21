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

// Functions for rendering chat widgets

// Just return a div with an active member count.
function render_active_members() {
	DatabaseConnection::getInstance();

	$statement = DatabaseConnection::$pdo->query("SELECT count(*) FROM ppl_online WHERE member = true AND activity > (now() - CAST('30 minutes' AS interval)) UNION SELECT count(*) FROM ppl_online WHERE member = true");
	$members = $statement->fetchColumn();
	$membersTotal = $statement->fetchColumn();

	return
        "<div class='active-members-count'>
            Active Members:  ".($members ? $members : '0')." / ".($membersTotal ? $membersTotal : '0')."
        </div>";
}

// Get all the chat messages info.
function get_chats($chatlength) {
	$limit = ($chatlength ? 'LIMIT :limit' : '');
	$bindings = array();
	if($limit){
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

/**
 * Render the div full of chat messages.
 * @param $chatlength Essentially the limit on the number of messages.
**/
function render_chat_messages($chatlength, $show_elipsis=null) {
	// Eventually there might be a reason to abstract out get_chats();
	$chats = get_chats($chatlength);
	$message_count = get_chat_count();
	$res = "<dl class='chat-messages'>";

	$previous_date = null;
	$previous_ago  = null;

	foreach ($chats AS $chat_message) {
		$chat_message['message'] = trim($chat_message['message']);

		if (!empty($chat_message['message'])) {
			// Check for the x time ago message.
			$l_ago = time_ago($chat_message['ago'], $previous_date);
			$template_data = array('sender_id'=>$chat_message['sender_id'], 'sender_name'=>$chat_message['uname'], 'message'=>$chat_message['message'], 'message_date'=>$chat_message['date']);

			if ($l_ago != $previous_ago) {
				$template_data['ago'] = $l_ago;
			}

			$res .= render_template('chatmessage.tpl', $template_data);

			$previous_date = $chat_message['ago']; // Store just prior date.
			$previous_ago  = $l_ago; // Save the prior ago message.
		}
	}

	$res .= "</dl>";
    
	if ($show_elipsis && $message_count > $chatlength) { // to indicate there are more chats available
		$res .= ".<br>.<br>.<br>";
	}

	return $res;
}

// parse the date/time for the chat.
function time_ago($time, $previous) {
	// Need to modify the database calls in order to retrieve the right ago message stuff.
	$time_array = time_to_array($time);
	$similar = false;
	$res = null;

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
	} elseif($time_array['hours'] > 0) {
		$res = (int)$time_array['hours'].(1 == (int)$time_array['hours'] ? ' hour' : ' hours');
	} else {
		$res = (int)$time_array['minutes'].(1 == (int)$time_array['minutes'] ? ' minute' : ' minutes');
	}

	return '('.$res.' ago)';
}
?>
