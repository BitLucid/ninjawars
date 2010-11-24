<h1>Status</h1>

<ul id='event-list'>
{foreach from=$events item="loop_event"}
	{include file="single_event.tpl" event=$loop_event}
{foreachelse}
  You have not been attacked recently.
{/foreach}
</ul>
