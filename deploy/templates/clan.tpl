<h1 id='clan-page-title'>Clan Panel</h1>

<section id='clan-page-section' class='clan' style='margin-top:2rem;margin-bottom:2rem;'>

	<nav class="navigation" rel="nav">
	<ul class="menu">
		<li><a href="/clan/list">Clan List</a></li>
	{if isset($myClan)}
		<li><a href="/clan/view?clan_id={$myClan->id|escape}">My Clan</a></li>
	{/if}
	</ul>
	</nav>

	{include file="flash-message.tpl"}

	{foreach from=$pageSections item="part"}
		{include file="clan.$part.tpl"}
	{/foreach}

</section>

<script type="text/javascript" src="{cachebust file="/js/clan.js"}"></script>
