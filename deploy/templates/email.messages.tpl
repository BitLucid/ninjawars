<div>
	Here are your latest messages:
</div>

<div>

	<dl id='message-list' class='message-list'>
	{foreach from=$messages item="loop_message"}
		{include file='message.single.tpl' message=$loop_message}
	{/foreach}
	</dl>

</div>
