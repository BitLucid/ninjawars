<!-- Js at the bottom of this template -->
{literal}
<style type='text/css'>
#full-chat{
	font-size:1.1em;
}
#full-chat #view-all{
	display:block;border:1px dashed blue;margin-top:2em;text-align:center;font-size:1.3em;
}
#full-chat .link-as-button{
	margin-bottom:.5em;margin-top:1em;margin-right:1.5em;
}
#full-chat .chat-submit input[type=submit]{
	padding:.2em .4em;font-size:1.3em;font-weight:bolder;
}
#full-chat .float-right{
	float:right;
}
</style>
{/literal}


<h1>Chat Board</h1>

<div id='full-chat'>
{if is_logged_in()}
  <form class='chat-submit' id="post_msg" action="{$target|escape}" method="post" name="post_msg">
    <div>
      <input id="message" type="text" size="{$field_size}" maxlength="250" name="message" autofocus autocomplete='off' class="textField">
      <input id="command" type="hidden" value="postnow" name="command">
      <input name='chat_submit' type='hidden' value='1'>
      <input type="submit" value="Chat" class="formButton">
{/if}

<a class='link-as-button float-right' href="village.php?chatlength=100">Refresh</a>


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
{if $more_chats_to_see}
  <a id='view-all' href='village.php?view_all=1'>View All Chat Messages</a>
{/if}
</div>

<script src="/js/jquery.linkify.js" type="text/javascript"></script>
<script type="text/javascript">
{literal}
$(function(){
	$(".chat-message").linkify({ target: "_blank" });
	setInterval(refreshpagechat, 30*1000); // Periodically refresh the page.
});
</script>
{/literal}