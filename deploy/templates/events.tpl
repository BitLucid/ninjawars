<h1>Status</h1>
{* Uses has_clan to set the clan chat tab if needed *}
{include file='message-tabs.tpl' current='status'}

<dl id='event-list'>
{if isset($events)}
  {foreach from=$events item="loop_event"}
      {include file="event.single.tpl" event=$loop_event}
  {foreachelse}
      <div class='ninja-info'>Nothing has happened to you recently.</div>
  {/foreach}
{/if}
</dl>
