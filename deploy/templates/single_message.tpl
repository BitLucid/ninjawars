    <dt class='user {if $last_user_message eq $message.send_from}repeated{/if}'>
    	&lt;<a target='main' href='player.php?player_id={$message.send_from|escape:'url'}'>{$message.from|escape}</a>&gt; 
    </dt>
    <dd class='user-message{if $message.unread} message-unread{/if}'>
    	{$message.message|escape|replace_urls|markdown}
    </dd>
    
    {assign var='last_user_message' value=$message.send_from}
