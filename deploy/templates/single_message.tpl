<style>
{literal}
	dl, dt, dd{
		margin:0;
		padding:0;
	}
	dl {
	 width:100%;
	 overflow:hidden;
	}
	dt {
	 float:left;
	 width:20%; /* adjust the width; make sure the total of both is 100% */
	 min-width:160px;
	}
	dd {
	 float:left;
	 width:70%; /* adjust the width; make sure the total of both is 100% */
	}
{/literal}
</style>

    <dt class='user {if $last_user_message eq $message.send_from}repeated{/if}'>
    	&lt;<a target='main' href='player.php?player_id={$message.send_from|escape:'url'}'>{$message.from|escape}</a>&gt; 
    </dt>
    <dd class='user-message{if $message.unread} message-unread{/if}'>
    	{$message.message|escape|replace_urls|markdown}
    </dd>
    
    {assign var='last_user_message' value=$message.send_from}
