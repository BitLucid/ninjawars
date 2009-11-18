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
  global $sql;
  $sql->Insert("INSERT INTO chat (chat_id, sender_id, message, date) 
        VALUES (default,'$user_id','".sql($msg)."',now())");
        
  // could add channels later.
}


 

// Functions for rendering chat widgets

// Just return a div with an active member count.
function render_active_members($sql){
    $members = $sql->QueryItem(
        "select count(*) from ppl_online where member = true AND activity > (now() - CAST('30 minutes' AS interval))");
    $membersTotal = $sql->QueryItem("select count(*) from ppl_online where member = true");
    return
        "<div class='active-members-count'>
            Active Members:  ".($members?$members : '0')." / ".($membersTotal?$membersTotal : '0')."
        </div>";
}

/**
 * Render the div full of chat messages.
 * @param $chatlength Essentially the limit on the number of messages.
**/
function render_chat_messages($chatlength, $show_elipsis=null){
    // Eventually there might be a reason to abstract out get_chats();
    $sql = new DBAccess();
    $sql->Query("SELECT sender_id, uname, message, age(now(), date) as ago FROM chat join players on chat.sender_id = player_id ORDER BY chat_id DESC LIMIT $chatlength");// Pull messages
    $chats = $sql->fetchAll();
    $message_rows = '';
    $messageCount = $sql->QueryItem("select count(*) from chat");
    if (!isset($show_elipsis) && $messageCount>$chatlength){
		$show_elipsis = true;
    }
    $res = "<div class='chatMessages'>";
    $previous_date = null;
    $skip_interval = 3; // minutes
    foreach($chats AS $messageData) {
    	$l_ago = time_ago($messageData['ago'], $previous_date);
		$message_rows .= "<li>&lt;<a href='player.php?player_id={$messageData['sender_id']}'
		     target='main'>{$messageData['uname']}</a>&gt; ".out($messageData['message'])." <span class='chat-time'>{$l_ago}</span></li>";
		$previous_date = $messageData['ago']; // Store just prior date.
    }
    $res .= $message_rows;
    if ($show_elipsis){ // to indicate there are more chats available
		$res .= ".<br>.<br>.<br>";
    }
    $res .= "</div>";
    return $res;
}

// parse the date/time for the chat.
function time_ago($time, $previous){
	$time_array = array_reverse(preg_split("/(\D)/", $time)); // Split on non-digits.
	$similar = false;
	$res = null;
	if($previous){
		$previous_array = array_reverse(preg_split("/(\D)/", $previous)); // Split on non-digits.
		/* If the time is substantially different from the previous (1 minute or more),
		then mark down how long ago the time was and return it (to be displayed after the chat 
		If the minutes, hours, and days are similar between two messages, they're similar.
		*/
		if($time_array[2] == $previous_array[2] && $time_array[3] == $previous_array[3] && 
			((!isset($time_array[4]) || !isset($previous_array[4])) || $time_array[4] == $previous_array[4])){
			$similar = true;
		}
	}
	if(!$similar){ // Display time if no previous or non-similar previous time.
		$ago = false;
		if(isset($time_array[4]) && $time_array[4]>0){
			$res .= (int)$time_array[4].(1==(int)$time_array[4]?' day' : ' days');
			$ago = true;
		}
		if($time_array[3]>0){
			if($ago){
				$res .= ', ';
			}
			$res .= (int)$time_array[3].(1==(int)$time_array[3]?' hour' : ' hours');
			$ago = true;
		}
		if($time_array[2]>0){
			if($ago){
				$res .= ', ';
			}
			$res .= (int)$time_array[2].(1==(int)$time_array[2]?' minute' : ' minutes');
			$ago = true;
		}
		if($ago){
			$res = '('.$res.' ago)';
		}
	}
	return $res;
}

// Render the "refresh chat periodically" js.
function render_chat_refresh($not_mini=null){
	// TODO: this chat javascript update needs to be cleaned up and put into the js file.
    $location = "mini_chat.php";
    $frame = 'mini_chat';
    if($not_mini){
        $location = "village.php";
        $frame = 'main';
    }
    ob_start();
    ?>

    <script type="text/javascript">
    function refreshpage<?php echo $frame; ?>(){
      parent.<?php echo $frame; ?>.location="<?php echo $location; ?>";
    }
    setInterval("refreshpage<?php echo $frame; ?>()",300*1000);
    </script>
    <?php
    $res = ob_get_contents();
    ob_end_clean();
    return $res;
}

/**
 * Display the chat input form.
**/
function render_chat_input($target='mini_chat.php', $field_size=20){
    return
    "<form class='chat-submit' id=\"post_msg\" action=\"$target\" method=\"post\" name=\"post_msg\">\n
    <input id=\"message\" type=\"text\" size=\"$field_size\" maxlength=\"250\" name=\"message\" class=\"textField\">\n
    <input id=\"command\" type=\"hidden\" value=\"postnow\" name=\"command\">
    <input name='chat_submit' type='hidden' value='1'>
    <input type=\"submit\" value=\"&gt;\" class=\"formButton\">\n
    </form>\n";
}

?>
