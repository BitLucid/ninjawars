{if $startingHealth < $finalHealth}
<p>
  A monk tends to your wounds and you are {if $fully_healed}fully healed{else}healed to {$finalHealth|escape} health{/if}.
</p>
	{if $finalHealth}
<span style='width:10em;display:inline-block;'>
{include file="health_bar.tpl" health=$finalHealth health_percent=$health_percent}
</span>
	{/if}
	{if $has_chi}
<p>Your chi is strong and you recover faster than expected!</p>
	{/if}
	{if !$fully_healed}
<div>
  <a class='btn btn-default' href='shrine.php?command=heal&amp;heal_points=max'>Heal Fully</a>
</div>
	{/if}
{/if}
