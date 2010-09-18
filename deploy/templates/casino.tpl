<h1>Casino</h1>

<div class="description">
  <div>You walk down the alley towards a shadowed door. As you enter the small casino, a guard eyes you with caution.</div>
  <div style="margin-top: 15px;margin-bottom: 15px;">You walk towards the only table with an attendant. He shows you a shiny coin with a dragon on one side and a house on the other.</div>
  <div>"Place your bet, call the coin in the air, and let's see who's lucky today!"</div>
</div>

<hr>

<div>Welcome to the Casino, {$username|escape}!</div>

{if $state eq $templatelite.const.CASINO_NO_GOLD}
<div>You do not have that much gold.</div>
{elseif $state eq $templatelite.const.CASINO_LOSE}
<div class='ninja-notice'>You lose the coin toss!</div>
{elseif $state eq $templatelite.const.CASINO_WIN}
<div>You win the coin toss!</div>
{elseif $state eq $templatelite.const.CASINO_DEFAULT}
<div>The maximum bet at this table is 1,000 gold.</div>
{/if}

<form id="coin_flip" action="casino.php" method="post" name="coin_flip">
  <div>
    Bet: <input id="bet" type="text" value='{$bet}' size="3" maxlength="4" name="bet" class="textField">
    &nbsp;&nbsp;<input type="submit" value="Place bet" class="formButton">
  </div>
</form>

<div class='gold-count'>Current Gold: {$current_gold}</div>
