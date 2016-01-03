<!-- Js at the bottom of this template -->
{literal}
<style type='text/css'>
#full-chat{
	font-size:1.1em;
}
#full-chat #view-all{
	display:block;border:1px dashed blue;margin-top:2em;text-align:center;font-size:1.3em;
}
#full-chat .float-right{
	float:right;
}
#full-chat .chat-submit #message{
	max-width:50%;
}
</style>
{/literal}

<section id='full-chat' class='dont-break-out'>

	<h1>Chat Board</h1>

	{if $error}<div class='error'>{$error}</div>{/if}

  <a class='btn btn-default float-right' href="village.php?chatlength=100">Refresh</a>

{if is_logged_in()}
  <form class='chat-submit' id="post_msg" action="{$target|escape}" method="post" name="post_msg">
    <div>
      <input id="message" type="text" size="{$field_size}" maxlength="250" name="message" required autofocus autocomplete='off' class="textField" title='Chat message should not be empty.'>
      <input id="command" type="hidden" value="receive" name="command">
      <input name='chat_submit' type='hidden' value='1'>
      <input type="submit" value="Chat" class="btn btn-primary">
    </div>
  </form>
{/if}

  <dl class='chat-messages'>
{foreach from=$chats item="record"}
	{assign var="message" value=$record.message|trim}

	{if $message}
		{include file="chatmessage.tpl" sender_id=$record.sender_id sender_name=$record.uname message=$message message_date=$record.date}
	{/if}
{/foreach}
  </dl>
{if $more_chats_to_see}
  <a id='view-all' href='village.php?view_all=1'>View All Chat Messages</a>
{/if}
</section>

<script type="text/javascript" src="js/chat.js"></script>
<script src="/js/jquery.linkify.js" type="text/javascript"></script>
<script type="text/javascript">
{literal}
$(function(){
	$(".chat-message").linkify({ target: "_blank" });
	setInterval(refreshpagechat, 30*1000); // Periodically refresh the page.
});
</script>
{/literal}