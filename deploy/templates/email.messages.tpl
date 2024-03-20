<h1>Here are your latest messages:</h1>

<div class='glassbox'>
	<div style='display:inline-block'>
		<dl id='message-list' class='message-list'>
		{foreach from=$messages item="loop_message"}
			{include file='message.single.tpl' message=$loop_message}
		{/foreach}
		</dl>

	</div>
</div>
