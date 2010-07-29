<dt class='chat-author'>
&lsaquo;<a href='player.php?player_id={$sender_id}' target='main'>{$sender_name|escape}</a>&rsaquo;</dt>
<dd class='chat-message'>{$message|escape}{if isset($ago)}<abbr class='chat-time timeago' title='{$message_date}'>{$ago}</abbr>{/if}</dd>
