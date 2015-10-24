<p>
  A monk tends to your wounds and you are {if $player->hurt_by() lte 0}fully healed{else}healed to {$player->health()|escape} health{/if}.
</p>
<span style='width:10em;display:inline-block;'>
{include file="health_bar.tpl" health=$player->health() health_percent=$player->health_percent()}
</span>
{if $has_chi}
<p>Your chi is strong and you recover faster than expected!</p>
{/if}
{if $player->hurt_by() gt 0}
<div>
  <a class='btn btn-default' href='shrine.php?command=heal&amp;heal_points=max'>Heal Fully</a>
</div>
{/if}
