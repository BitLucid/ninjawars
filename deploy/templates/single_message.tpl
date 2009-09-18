<li>
<a href='player.php?player_id={$message.from_id}'>{$message.from}</a> 
<span id='user-message{if !$message.read} message-read{/if}'>{$message.message}</span>
</li>
