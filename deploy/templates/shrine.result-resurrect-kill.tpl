<p class='ninja-notice'>You lose {$killCost|escape} kill.</p>
{* Commented out as redundant for now. <p class='ninja-notice'>Adjusted Kills after returning to life: {$player->kills|escape}</p>*}
<div class='parent'>
	<div class='child'>
		<span class='health-bar-container'>
		{include file="health_bar.tpl" health=$player->health() level=$player->level}
		</span>
	</div>
</div>
