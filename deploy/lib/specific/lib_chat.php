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
    $members = $sql->QueryItem("select count(*) from ppl_online where member = true AND activity > (now() - CAST('30 minutes' AS interval))");
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
    $res = "<div id='chatMessages' style='float:;margin-top:0em'>";
    foreach($chats AS $messageData) {
	// *** PROBABLY ALSO A SPEED PROBLEM AREA.
	$message_rows .= "[<a href='player.php?player={$messageData['send_from']}'
	     target='main'>{$messageData['send_from']}</a>]: {$messageData['message']}<br>\n";
    }
    $res .= $message_rows;
    if ($show_elipsis){ // to indicate there are more chats available
	$res .= ".<br>.<br>.<br>";
    }
    $res .= "</div>";
    return $res;
}

// Render the "refresh chat periodically" js.
function render_chat_refresh(){
    ob_start();
    ?>

    <script type="text/javascript">
    function refreshpage()
    {
      parent.mini_chat.location="mini_chat.php";
    }
    setInterval("refreshpage()",300*1000);
    </script>
    <?php
    $res = ob_get_contents();
    ob_end_clean();
    return $res;
}

/**
 * Display the chat input form.
**/
function render_chat_input(){
    return
    "<form id=\"post_msg\" action=\"mini_chat.php\" method=\"post\" name=\"post_msg\" style=\"margin-top:0em;margin-bottom:0.5em\">\n
    <input id=\"message\" type=\"text\" size=\"20\" maxlength=\"490\" name=\"message\" class=\"textField\">\n
    <input id=\"command\" type=\"hidden\" value=\"postnow\" name=\"command\">
    <input type=\"submit\" value=\"Send\" class=\"formButton\">\n
    </form>\n";
}

?>
