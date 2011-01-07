{if isset($not_mini) and $not_mini}
	{assign var="location" value="mini_chat.php"}
	{assign var="frame" value='mini_chat'}
{else}
	{assign var="location" value="village.php"}
	{assign var="frame" value='main'}
{/if}

<h1>Chat Board</h1>

<script type="text/javascript">
function refreshpage{$frame}() {literal}{{/literal}
	parent.{$frame}.location = "{$location}";
{literal}}{/literal}
setInterval(refreshpage{$frame}, 300*1000);
{literal}
window.onload = function(){
	$(document.getElementById('message')).focus();
};
{/literal}
</script>
{literal}
<style type='text/css'>
	#full-chat{
		font-size:1.1em;
	}
</style>
{/literal}

<div id='full-chat'>
{if is_logged_in()}
  <form class='chat-submit' id="post_msg" action="{$target|escape}" method="post" name="post_msg">
    <div>
      <input id="message" type="text" size="{$field_size}" maxlength="250" name="message" class="textField">
      <input id="command" type="hidden" value="postnow" name="command">
      <input name='chat_submit' type='hidden' value='1'>
      <input type="submit" value="Chat" class="formButton" style='padding:.2em .4em;font-size:1.3em;font-weight:bolder'>
{/if}

<a class='link-as-button' style='margin-bottom:.5em;margin-top:1em;margin-left:25%;' href="village.php?chatlength=100">Refresh</a>


{if is_logged_in()}
    </div>
  </form>
{/if}

  <div class='active-members-count'>
    Ninjas: {$active_chars} Active / {$chars_online} Online / {$total_chars} Total
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
</div>
