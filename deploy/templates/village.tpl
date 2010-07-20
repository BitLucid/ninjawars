{if isset($not_mini) and $not_mini}
	{assign var="location" value="mini_chat.php"}
	{assign var="frame" value='mini_chat'}
{else}
	{assign var="location" value="village.php"}
	{assign var="frame" value='main'}
{/if}

<h1>Chat Board</h1>
<p><a href="village.php?chatlength=50">Refresh</a><p>

<script type="text/javascript">
function refreshpage{$frame}() {literal}{{/literal}
	parent.{$frame}.location = "{$location}";
{literal}}{/literal}
setInterval(refreshpage{$frame}, 300*1000);
</script>

<div id='full-chat'>
{if is_logged_in()}
  <form class='chat-submit' id="post_msg" action="{$target|escape}" method="post" name="post_msg">
    <div>
      <input id="message" type="text" size="{$field_size}" maxlength="250" name="message" class="textField">
      <input id="command" type="hidden" value="postnow" name="command">
      <input name='chat_submit' type='hidden' value='1'>
      <button type="submit" value="&gt;" class="formButton">Chat</button>
    </div>
  </form>
{/if}
  <div class='active-members-count'>
    Ninjas: {$active_chars} Active / {$chars_online} Online / {$total_chars} Total
  </div>
{$chat_messages}
</div>
