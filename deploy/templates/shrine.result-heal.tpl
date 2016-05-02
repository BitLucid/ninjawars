<p>
  A monk tends to your wounds and you are {if $player->is_hurt_by() lte 0}fully healed{else}partly healed{/if}.
</p>
{if $has_chi}
<p>Your chi is strong and you recover faster than expected!</p>
{/if}
<div class='parent'>
	<div class='child'>
		<span class='health-bar-container'>
		{include file="health_bar.tpl" health=$player->health level=$player->level}
		</span>
	</div>
</div>
