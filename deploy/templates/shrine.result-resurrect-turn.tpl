<p class='ninja-notice'>Since you have no kills, your resurrection will cost you part of your life time.</p>
<p>Turn Cost: {$turnCost|escape}</p>
<p class='ninja-notice'>Adjusted Turns after returning to life: {$player->turns|escape}</p>
<span style='width:10em;display:inline-block;'>
{include file="health_bar.tpl" health=$player->health() level=$player->level}
</span>
