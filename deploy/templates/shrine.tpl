<h1>Shrine</h1>
<nav>
	<a href="/map" class="return-to-location block">Return to the Village</a>
</nav>
{include file="flash-message.tpl"}

<section class='action-area'>
{foreach from=$shrineSections item="part"}
	<div>
	{* e.g. see shrine.entrance.tpl for description and image *}
	{include file="shrine.$part.tpl"}
	</div>
{/foreach}
</section>
