<h1>Today's Duels</h1>

<div id='vicious-killer'>
    Current Fastest Killer: 
    <a id='vicious-killer-menu' href='player.php?player={$vicious_killer|escape:'url'|escape}'>{$vicious_killer|escape}</a>
</div>

<h3>Duel Log</h3>

{if count($duels) gt 0}
<ul id='duel-log'>
	{foreach item="duel" from=$duels}
  <li>
{include file="player-link.tpl" username=$duel.attacker id=$duel.attacker_id}
has dueled
{include file="player-link.tpl" username=$duel.defender id=$duel.defender_id}
and
	{if $duel.won}
won
	{else}
lost
	{/if}
for {$duel.killpoints} killpoints on {$duel.date}
  </li>
	{/foreach}
</ul>
{else}
<p>No duels for today yet.</p>
{/if}
