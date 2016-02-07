<h1>Shrine</h1>

{include file="clan.flash-message.tpl"}

<section class='action-area'>
{foreach from=$pageParts item="part"}
	{include file="shrine.$part.tpl"}
{/foreach}
</section>

<nav>
	<a href="/map" class="return-to-location block">Return to the Village</a>
</nav>
