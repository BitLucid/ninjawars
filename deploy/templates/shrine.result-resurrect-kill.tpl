<p>Kill Cost: {$killCost|escape}</p>
<p class='ninja-notice'>Adjusted Kills after returning to life: {$player->kills|escape}</p>
<span style='width:10em;display:inline-block;'>
{include file="health_bar.tpl" health=$player->health() health_percent=$player->health_percent()}
</span>
