<div id='mini-chat'>
  <div class='active-members-count'>
    Ninjas: {$members} Active / {$membersTotal} Online / {$total_chars} Total
  </div>
    
  <dl class='chat-messages'>
{assign var="previous_ago" value=''}
{assign var="previous_date" value=''}
{foreach from=$chats item="record"}
	{assign var="message" value=$record.message|trim}

	{if $message}
		{capture assign="l_ago"}
			{time_ago ago=$record.ago previous_date=$previous_date}
		{/capture}

		{if $l_ago neq $previous_ago}
			{assign var="ago" value=$l_ago}
		{else}
			{assign var="ago" value=''}
		{/if}

		{include file="chatmessage.tpl" sender_id=$record.sender_id sender_name=$record.uname message=$message message_date=$record.date ago=$ago}
		{assign var="previous_date" value=$record.ago}
		{assign var="previous_ago" value=$l_ago}
	{/if}
{/foreach}
  </dl>
{if $message_count > $chatlength}
.<br>.<br>.<br>
{/if}

</div>
