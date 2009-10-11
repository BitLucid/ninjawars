<?php
/*
 * Chat helper functions.
 *
 * @package messages
 * @subpackage chat
 */

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
function render_chat_messages($sql, $chatlength, $show_elipsis=null){
    // Eventually there might be a reason to abstract out get_chats();
    $sql->Query("SELECT send_from, message FROM chat ORDER BY id DESC LIMIT $chatlength");// Pull messages
    $chats = $sql->fetchAll();
    $message_rows = '';
    $messageCount = $sql->QueryItem("select count(*) from chat");
    if (!isset($show_elipsis) && $messageCount>$chatlength){
	$show_elipsis = true;
    }
    $res = "<div class='chatMessages'>";
    foreach($chats AS $messageData) {
	// *** PROBABLY ALSO A SPEED PROBLEM AREA.
	$message_rows .= "<a href='player.php?player={$messageData['send_from']}'
	     target='main'>{$messageData['send_from']}</a> ".out($messageData['message'])."<br>\n";
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
