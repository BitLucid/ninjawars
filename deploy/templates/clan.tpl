<h1 id='clan-page-title'>Clan Panel</h1>

<section id='clan-page-section' class='clan'>

<ul>
	<li><a href="clan.php?command=list">Clan List</a></li>
{if $myClan}
	<li><a href="clan.php?command=view&amp;clan_id={$myClan->getID()|escape}">My Clan</a></li>
{/if}
</ul>

{include file="clan.flash-message.tpl"}

{foreach from=$pageParts item="part"}
	{include file="clan.$part.tpl"}
{/foreach}

</section>

<script type="text/javascript" src="/js/clan.js"></script>
