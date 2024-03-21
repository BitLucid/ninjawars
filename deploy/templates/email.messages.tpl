<h1>Here are your latest messages:</h1>

<style>
.messages-container {
	display:inline-block;
}
</style>
<div class='glassbox'>
	<div class='messages-container'>
		<dl id='message-list' class='message-list'>
		{foreach from=$messages item="loop_message"}
			{include file='message.single.tpl' message=$loop_message}
		{/foreach}
		</dl>

	</div>
</div>
