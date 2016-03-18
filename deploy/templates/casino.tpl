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
</style>
{/literal}




<h1>Casino</h1>

<div class="description" style='border-bottom:1px solid gold'>
  <p>You walk down the alley towards a shadowed door on a low, squat building with golden gilt peeling off of the entranceway. As you enter the small casino, <a href='/npc/attack/guard'>a guard</a> eyes you with caution.</p>
  <p style="margin-top: 15px;margin-bottom: 15px;">No-one else is in the casino.  You walk towards the only table with a wizened old man, missing most of his teeth, behind it. He shows you a shiny coin.</p>
  <p> The old man says <span class='speech'>Welcome to the Casino, {if $player}{$player->name()|escape}{else}Stranger{/if}!</span>

  <p class='speech'>Place your bet, call the coin in the air, and let's see who's lucky today!</p>
</div>

<div id='casino-betting'>
{foreach from=$pageParts item="part"}
	{include file="casino.$part.tpl"}
{/foreach}

	<form id="coin_flip" class='js-hooked' action="/casino/bet" method="post" name="coin_flip">
	  <div>
		Bet: <input id="bet" type="text" size="3" maxlength="4" name="bet" class="textField">
		&nbsp;&nbsp;<input type="submit" value="Place bet" class="formButton">
	  </div>
	</form>

	<div class='gold-count'>Current Gold: 石{$player->gold|number_format:0|escape}</div>
</div><!-- End of betting div -->


<nav>
  <a href="/map" class="return-to-location block">Return to the Village</a>
</nav>
<script src='{cachebust file="/js/casino.js"}'></script>
