<h1>Bath House</h1>

<nav>
  <a href="/map" class="return-to-location block">Return to the Village</a>
</nav>

<div class='description glassbox'>
  <figure class='float-left glassbox'>
    <img 
        src='/images/scenes/bath-house-interior.jpg' 
        width='500' 
        class='img-fluid mx-auto d-block'
        alt='Inside The Bath House' />
  </figure>
  You enter the steaming confines of the bath house.
  An attendant takes your clothes and concealed weaponry with a knowing smile.
  Shedding your clothes, you ease into a large tiled tub that is filled with steaming water and exotic bath salts.
  <p>While your tensions melt away, you listen to the conversations around you that carry through the paper thin walls, telling of legendary exploits and dark deeds.</p>
</div>

<h3>Rumors</h3>

<div id='vicious-killer'>
    Current Fastest Killer:
    <a id='vicious-killer-menu' href='/player?player={$vicious_killer|escape:'url'|escape}'>
    	{$vicious_killer|escape}
    </a>
</div>

{if count($duels) gt 0}
<p>Talk of recent notable duels:</p>
<ul id='duel-log' style='overflow-y: auto;max-height:20rem;'>
	{foreach item="duel" from=$duels}
  <li>
  {include file="player-link.tpl" username=$duel.attacker id=$duel.attacker_id} has dueled {include file="player-link.tpl" username=$duel.defender id=$duel.defender_id} and {if $duel.won}won{else}lost{/if} for {$duel.killpoints} killpoints on {$duel.date}
  </li>
	{/foreach}
</ul>
{else}
<p>No fights have broken out yet today.</p>
{/if}

<div class='glassbox'>
  {if $stats['rich_haul'] > 0}
    <p>The attendants mention in your earshot that they heard of someone carrying more than <span class='gold-count'>石{$stats['rich_haul']} gold</span> recently.</p>
  {else}
    <p>The geisha complain about how stingy the patrons have been recently.</p>
  {/if}

  <p>
    From the talk you hear of <strong>{$recently_active} traveler{if $recently_active neq 'one'}s{/if}</strong> who recently passed through.
  </p>
</div>

<div class='glassbox'>
  You hear of {$stats['player_count']} ninja in the lands around the village.
</div>
