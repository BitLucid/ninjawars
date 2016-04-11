<div style='clear:both'>
<ul id='recent-attackers'>
{foreach from=$recent_attackers item=l_attacker}
	{if $l_attacker.health < 1}
		{assign var="status_class" value="status-dead"}
	{else}
		{assign var="status_class" value=""}
	{/if}

  <li class='recent-attacker {$status_class}'><a href='/player?player_id={$l_attacker.send_from|escape:'url'|escape}'>{$l_attacker.uname|escape}</a></li>
{/foreach}
</ul>
</div>
