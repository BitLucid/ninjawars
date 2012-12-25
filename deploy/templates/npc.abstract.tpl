{literal}
<style>
article nav{
	margin-top:1.5em;
	margin-left:.5em;
}
</style>
{/literal}
	
	
  <article>
	<h2>{$display_name|escape}</h2>
	
	<div style='width:80%;margin:0 10%'>
	{if $image_path}
		<figure style='margin:.5em auto .5em;text-align:center'>
		  <img src='{$image_path}' alt='Creature'>
		</figure>
	{/if}

	{if $npc_stats.short}
	<p>The {$display_name|escape} {$npc_stats.short}.</p>
	{/if}
	
	<p>The {$display_name|escape} wounds you for {$attack_damage} health.</p>
	{if $statuses}
	<p>The {$display_name|escape}'s strike leaves you <span class='{$statuses}'>{$statuses}</span>.</p>
	{/if}
	{if $victory}
		<p class='ninja-notice'>You slay the {$display_name|escape}!</p>

		{if $added_bounty}
		<p class='bounty-notice'>A bounty of {$added_bounty} gold has been placed on your head!</p>
		{/if}

		<p>You gather <span class='gold'>{$received_gold} 
			gold</span>{if $received_item} and a {$received_item}{/if}.</p>
			
	{else}
		<div class='ninja-error'>The {$display_name|escape} has killed you!</div>
	{/if}

	</div>
	<nav>
		<a href='npc.php?victim={$victim|escape|escape:'url'}' class='attack-again'>Attack another {$display_name|escape}</a>
	</nav>
  </article>
