<h1>Shrine</h1>

<style type='text/css'>
{literal}
.formButton {
	font-size: 1.5em !important;
}
form {
	text-align:center;
}
{/literal}
</style>

  
<section class="description">

  <figure style='display:inline-block;float:right'>
    <img src='/images/scenes/shrine_pagoda.png' alt='' title='Shrine Pagoda'>
  </figure>


  <div style="margin-bottom: 1.5em;">
    The shrine to the gods is peacefully quiet as you enter. The sound of flowing water soothes your mind.
  </div>
  <p>A monk with a reed hood over his face looks ready to play a reed flute in one corner of the shrine.</p>
  <div id='shrine-music' style='width:40%;'>
	{include file='music.tpl'}
  </div>
  
  <div>A monk approaches you quietly and asks, <em class='speech'>Are you in need of healing?</em></div>
</section>


<section class='action-area'>
{if !$username}
<div id='ninja-notice'>You have no need of healing.</div>
{else}
	{if !$player_health}
<form action="shrine_mod.php" method="post">
  <span class="brownHeading">Resurrect</span>
  <p>Resurrect to return to life.</p>
		{if $freeResurrection}
  <p style="color: red;">
    Since you have not killed more than twenty ninja or gained beyond five levels, you will not lose power by resurrecting!
  </p>
		{else}
  <p>You will lose a kill point for every resurrection. &nbsp;</p>
		{/if}

  <p>
    <input type="hidden" name="restore" value="1">
    <input type="submit" value="Return to life" class="formButton">
  </p>
</form>
<hr>
	{elseif $at_max_health}
<p class='fancy-ornamental-font comforting'>You are at full health.</p>
	{else}
<form id="max_heal_form" action="shrine_mod.php" method="post" name="max_heal_form" style='margin: .5em auto .5em;text-align:center'>
  <div>
    <div><em class='speech'>How much healing do you need?</em></div>
    <input id="max_heal" type="hidden" value="1" name="max_heal">
    <input type="submit" value="Full Heal" class="formButton" style='width:90%'>
  </div>
</form>
<form id="heal_form" action="shrine_mod.php" method="post" name="heal_form">
  <div style="margin-top: 10px;">
    <input type="submit" value="Heal" class="formButton">
    <input id="heal_points" type="text" size="3" maxlength="4" name="heal_points" class="textField" style='font-size:1.1em'> HP
    <input id="healed" type="hidden" value="1" name="healed">
  </div>
</form>
	{/if}


	{if $poisoned && $player_health}
<hr>
<form action="shrine_mod.php" method="post">
  <span class="brownHeading">Antidote(remove poison)</span>
  <p>
    Cure Poison effect, Cost: 50 gold.
    <input type="hidden" name="poisoned" value="1">
    <input type="submit" value="Antidote" class="formButton">
  </p>
</form>
	{/if}


{/if}<!-- End of if username block -->
</section>

<nav>
	<a href="map.php" class="return-to-location block">Return to the Village</a>
</nav>
