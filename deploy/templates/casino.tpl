{literal}
<style type="text/css">
#betting #results{
	display:inline-block;
	margin:.5em auto;
	color:black;
	background-color:#EBED7F;
	box-shadow: 3px 3px 5px #888;
	border-radius:.3em;
	padding:1em;
}
#results .lose{
	color:brown;
	font-weight:bold;
}
#results .win{
	font-weight:bold;
}
.toss{
	font-size:5em;
	display:inline-block;
	font-weight:normal;
}
#casino-betting{
	margin: .5em auto;text-align:center;
}
.casino-area .description p + p {
	margin: 1rem 0;
}

.your-loot-container{
	display:flex;
	justify-content: flex-start;
}
.your-loot{
	padding: 2rem;
	border: thin solid rgb(66, 66, 66);
}
</style>
{/literal}



<section class='casino-area'>

	<h1>Casino</h1>

	<nav>
		<a href="/map" class="return-to-location block">Return to the Village</a>
	</nav>

	{include file="flash-message.tpl"}

	<div class='overall'>
		<figure class='float-left glassbox'>
			<img 
				src='/images/scenes/casino-red.jpg' 
				width='500'
				class='img-fluid mx-auto d-block'
				alt='Working in Fields of Grain' />
		</figure>
		<div class='glassbox'>
			<div class="description">
				<p>You walk down the alley towards a shadowed door on a low stone building with gilt peeling off around it's door. As you enter the small casino, <a href='/npc/attack/guard'>a guard</a> eyes you with caution.</p>
				<p>No-one else is in the casino.  You walk towards the only table with a wizened old man, missing most of his teeth, behind it. He shows you a shiny coin.</p>
				<p> The old man says <span class='speech'>Welcome to the Casino, {if $player && $player->name()}{$player->name()|escape}{else}Stranger{/if}!</span>

				<p class='speech'>Place your bet, call the coin in the air, and let's see who's lucky today!</p>
			</div>
		</div>
	</div>

	<div id='casino-betting'>
		{if $pageParts}
			{foreach from=$pageParts item="part"}
			{include file="casino.$part.tpl"}
			{/foreach}
		{/if}

		<form id="coin_flip" class='js-hooked' action="/casino/bet" method="post" name="coin_flip">
			<div>
			Bet: <input id="bet" type="text" size="3" maxlength="4" name="bet" class="textField" style='color:gold'>
			&nbsp;&nbsp;<input type="submit" value="Place bet" class="btn btn-primary">
			</div>
		</form>
	</div><!-- End of betting div -->

	<div class='your-loot-container'>
		<div class='your-loot'>
			<div>You have <span class='gold-count'>石{$player->gold|number_format:0|escape} gold</span></div>
		</div>
	</div>

</section>

<script src='{cachebust file="/js/casino.js"}'></script>
