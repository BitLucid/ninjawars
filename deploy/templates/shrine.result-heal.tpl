<style>
.shrine-health-bar{
	width:10em;display:inline-block;
}
</style>

<p>
  A monk tends to your wounds and you are {if $player->hurt_by() lte 0}fully healed{else}healed to {$player->health()|escape} health{/if}.
</p>
<span class='shrine-health-bar'>
{include file="health_bar.tpl" health=$player->health() health_percent=$player->health_percent()}
</span>
{if $has_chi}
<p>Your chi is strong and you recover faster than expected!</p>
{/if}
