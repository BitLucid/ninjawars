    <dt class='user {if isset($last_user_message) && $last_user_message eq $message.send_from}repeated{/if}'>
    	<a target='main' class='message-sender' href='player.php?player_id={$message.send_from|escape:'url'}'>{$message.from|escape}</a> 
    </dt>
    <dd class='user-message{if $message.unread} message-unread{/if}'>
    	{$message.message|escape|replace_urls|markdown}
    </dd>
    
    {assign var='last_user_message' value=$message.send_from}
