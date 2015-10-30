<h1>Status</h1>
{* Uses has_clan to set the clan chat tab if needed *}
{include file='message-tabs.tpl' current='status'}

<dl id='event-list'>
{foreach from=$events item="loop_event"}
	{include file="event.single.tpl" event=$loop_event}
{foreachelse}
  You have not been attacked recently.
{/foreach}
</dl>
