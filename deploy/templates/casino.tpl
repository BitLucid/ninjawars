<h1>Casino</h1>

<div class="description" style='border-bottom:1px solid gold'>
  <p>You walk down the alley towards a shadowed door on a low, squat building with golden guilt peeling off of the entranceway. As you enter the small casino, <a href='npc.php?attacked=1&victim=guard'>a guard</a> eyes you with caution.</p>
  <p style="margin-top: 15px;margin-bottom: 15px;">No-one else is in the casino.  You walk towards the only table with a wizened old man, missing most of his teeth, behind it. He shows you a shiny coin with a dragon on one side and a house on the other.</p>
  <p> The old man says <span class='speech'>Welcome to the Casino, {if !$username}Stranger{else}{$username|escape}{/if}!</span>

  <p class='speech'>Place your bet, call the coin in the air, and let's see who's lucky today!</p>
</div>
<div id='betting' style='margin: .5em auto;text-align:center'>
	{if $state eq $smarty.const.CASINO_CHEAT}
	<p class='speech'>Ah!  Trying to cheat the casino!  Foolish lout!  Now you'll get the reward you deserve!  Guards!</p>
	<p>The casino guards circle you, <span class='ninja-notice'>beat you to within an inch of your life</span>, and toss you at the entrance.</p>
	<p><span class='speech'>Better luck next time.</span> the old man cackles.</p>
	{elseif $state eq $smarty.const.CASINO_NO_GOLD}
	<div class='ninja-notice'>You do not have that much gold.</div>
	{elseif $state eq $smarty.const.CASINO_LOSE}
	<div>You lose the coin toss!</div>
	{elseif $state eq $smarty.const.CASINO_WIN}
	<div>You win the coin toss!</div>
	{elseif $state eq $smarty.const.CASINO_DEFAULT}
	<div>The maximum bet at this table is {$smarty.const.MAX_BET} gold.</div>
	{/if}

	<form id="coin_flip" action="casino.php" method="post" name="coin_flip">
	  <div>
		Bet: <input id="bet" type="text" value='{$bet}' size="3" maxlength="4" name="bet" class="textField">
		&nbsp;&nbsp;<input type="submit" value="Place bet" class="formButton">
	  </div>
	</form>

	<div class='gold-count'>Current Gold: {$current_gold}</div>
	
</div><!-- End of betting div -->
