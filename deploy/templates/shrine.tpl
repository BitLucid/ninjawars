<h1>Shrine</h1>

  
<section class="description">

  <figure style='float:right;display:inline-block'>
    <img src='/images/scenes/shrine_pagoda.png' class='float-left inline-block' alt='' title='Shrine Pagoda' width='200' height='310'>
  </figure>


  <p>
    The shrine to the gods is peacefully quiet as you enter. The sound of flowing water soothes your mind.
  </p>
  <p>A monk with a woven reed hood over his face prepares to play a bamboo flute in one corner of the shrine.</p>

  <div class='parent'>
    <div id='shrine-music' class='child'>
  	 {include file='music.tpl'}
    </div>
  </div>
  
  <div>A monk approaches you quietly and asks, <em class='speech'>Are you in need of healing?</em></div>
</section>


<section class='action-area'>
{if !$username}
<div id='ninja-notice'>You have no need of healing.</div>
{else}
	{if !$player_health}
<form action="shrine_mod.php" method="post" class='centered'>

  <p>
    <input type="hidden" name="restore" value="1">
    <input type="submit" value="Return to life" class="btn btn-vital">
  </p>

    {if !$freeResurrection}
  <p class='ninja-notice'>You will lose a kill point for every resurrection. &nbsp;</p>
    {/if}

</form>
<hr>
	{elseif $at_max_health}
<div class='parent thick'>
  <p class='btn btn-success child'>You are at full health.</p>
</div>
	{else}
<form id="max_heal_form" action="shrine_mod.php" method="post" name="max_heal_form" class='thick centered'>
  <div>
    <div><em class='speech'>How much healing do you need?</em></div>
    <input id="max_heal" type="hidden" value="1" name="max_heal">
    <input type="submit" value="Full Heal" class="btn btn-primary" style='min-width:90%'>
  </div>
</form>
<form id="heal_form" action="shrine_mod.php" method="post" name="heal_form">
  <div class='thick'>
    <input type="submit" value="Heal" class="btn btn-default">
    <input id="heal_points" type="text" size="3" maxlength="4" name="heal_points" class="textField" style='font-size:1.1em'> HP
    <input id="healed" type="hidden" value="1" name="healed">
  </div>
</form>
	{/if}


	{if $poisoned && $player_health}
<form action="shrine_mod.php" method="post">
  <p>
    Cure Poison effect, Cost: 100 gold.
    <input type="hidden" name="poisoned" value="1">
    <input type="submit" value="Get Antidote" class="btn btn-primary">
  </p>
</form>
<hr>
	{/if}


{/if}<!-- End of if username block -->
</section>

<nav>
	<a href="map.php" class="return-to-location block">Return to the Village</a>
</nav>
