<h1>Dojo</h1>

<div class="description">
  <div style="margin-bottom: 10px;">
    You walk up the steps to the grandest building in the village. The dojo trains many respected ninja.
  </div>
  <div>
    As you approach, you can hear the sounds of fighting coming from the wooden doors in front of you.
  </div>
</div>

{if !is_logged_in()}
<p>The guards at the door block your way, saying "Stranger, go on your way, you haven't the skill to enter here."</p>
{else}

	{if $dimMakAllowed}
		{include file="dojo_dimmak.tpl"}
	{/if}
	{if $classChangeAllowed}
		{include file="dojo_class_change.tpl"}
	{/if}

<a href="chart.php">Upgrade Chart</a><hr>

<div>Your current level is {$userLevel|escape}.</div>
<div style='margin-bottom: 10px;'>Your current kills are {$userKills|escape}.</div>
<div style='margin-bottom: 10px;'>Level {$nextLevel|escape} requires {$required_kills|escape} kills.</div>

	{if $upgrade_requested}
		{if $nextLevel > $max_level}
<div>There are no trainers that can teach you beyond your current skill. You are legendary among the ninja.</div>
		{elseif $userKills < $required_kills}
<div>You do not have enough kills to proceed at this time.</div>
		{else}
<div>Your trainer puts you through your paces and you learn a great deal from your bruises. Welcome to level {$userLevel|escape}!</div>
		{/if}
	{/if}
	{if $nextLevel gt $max_level}
<div>You enter the dojo as one of the elite ninja. No trainer has anything left to teach you.</div>
	{elseif $userKills lt $required_kills}
<div>Your trainer finds you lacking. You are instructed to prove your might against more ninja before you return.</div>
	{else}
<form id="level_up" action="dojo.php" method="post" name="level_up">
  <div style='margin-top: 10px;margin-bottom: 10px;'>
    <div>Do you wish to upgrade to level {$nextLevel|escape}?</div>
    <input id="upgrade" type="hidden" value="1" name="upgrade">
    <input type="submit" value="Upgrade" class="formButton">
  </div>
</form>
	{/if}
{/if}
