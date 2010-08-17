<h1>Battle Status</h1>
{if $turns lte 0}
You have no turns left today. Buy a speed scroll or wait for your turns to replenish.
{elseif $attacked == 1}
<p>Attacking...</p>
{include file=$npc_template}
{/if}
{if !$health}
<p class="ninja-notice">Go to the <a href="shrine.php">shrine</a> to resurrect.</p>
{else}
<a href="attack_player.php">Return to the Village</a>
{/if}
