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
    $sql->Query("SELECT sender_id, uname, message, date FROM chat join players on chat.sender_id = player_id ORDER BY chat_id DESC LIMIT $chatlength");// Pull messages
    $chats = $sql->fetchAll();
    $message_rows = '';
    $messageCount = $sql->QueryItem("select count(*) from chat");
    if (!isset($show_elipsis) && $messageCount>$chatlength){
	$show_elipsis = true;
    }
    $res = "<div class='chatMessages'>";
    foreach($chats AS $messageData) {
	// *** PROBABLY ALSO A SPEED PROBLEM AREA.
	$message_rows .= "<li>&lt;<a href='player.php?player_id={$messageData['sender_id']}'
	     target='main'>{$messageData['uname']}</a>&gt; ".out($messageData['message'])."</li>";
    }
    $res .= $message_rows;
    if ($show_elipsis){ // to indicate there are more chats available
	$res .= ".<br>.<br>.<br>";
    }
    $res .= "</div>";
    return $res;
}

// Render the "refresh chat periodically" js.
function render_chat_refresh($not_mini=null){
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
    "<form id=\"post_msg\" action=\"$target\" method=\"post\" name=\"post_msg\">\n
    <input id=\"message\" type=\"text\" size=\"$field_size\" maxlength=\"490\" name=\"message\" class=\"textField\">\n
    <input id=\"command\" type=\"hidden\" value=\"postnow\" name=\"command\">
    <input type=\"submit\" value=\"Send\" class=\"formButton\">\n
    </form>\n";
}

?>
