<h1>Bath House</h1>

<nav>
  <a href="/map" class="return-to-location block">Return to the Village</a>
</nav>

<div class='description'>
You enter the steaming confines of the bath house.
A Geisha takes your clothes and concealed weaponry with a knowing smile.
Shedding your clothes, you ease into a large copper tub that is filled with steaming water and exotic bath salts.
<p>While your tensions melt away, you listen to the conversations around you that carry through the paper thin walls, telling of legendary exploits and dark deeds.</p>
</div>

<div id='vicious-killer'>
    Current Fastest Killer:
    <a id='vicious-killer-menu' href='/player?player={$vicious_killer|escape:'url'|escape}'>
    	{$vicious_killer|escape}
    </a>
</div>

<h3>Rumors</h3>

{if count($duels) gt 0}
<ul id='duel-log'>
	{foreach item="duel" from=$duels}
  <li>
  {include file="player-link.tpl" username=$duel.attacker id=$duel.attacker_id} has dueled {include file="player-link.tpl" username=$duel.defender id=$duel.defender_id} and {if $duel.won}won{else}lost{/if} for {$duel.killpoints} killpoints on {$duel.date}
  </li>
	{/foreach}
</ul>
{else}
<p>No fights have broken out yet today.</p>
{/if}

<div>
  There are {$stats['player_count']} ninja around the village.
</div>
