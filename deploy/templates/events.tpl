<h1>Status</h1>

{include file='message-tabs.tpl' current='status'}

<ul id='event-list'>
{foreach from=$events item="loop_event"}
	{include file="single_message.tpl" message=$loop_event}
{foreachelse}
  You have not been attacked recently.
{/foreach}
</ul>
