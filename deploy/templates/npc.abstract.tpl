	
	<h2>{$display_name|escape}</h2>
	
	{if $image_path}
		<img src='{$image_path}' alt='Creature'>
	{/if}
	
	
	<p>The {$display_name|escape} wounds you for {$attack_damage} chakra.</p>
	{if $statuses}
	<p>The {$display_name|escape}'s strike leaves you <span class='{$statuses}'>{$statuses}</span>.</p>
	{/if}
	{if $victory}
		<p class='ninja-notice'>You slay the {$display_name|escape}!</p>
		<p>You receive <span class='gold'>{$received_gold} 
			gold</span>{if $received_item} and a {$received_item}{/if}.</p>
	{else}
		<div class='ninja-error'>The {$display_name|escape} has killed you!</div>
	{/if}

	<div>
		<a href='npc.php?victim={$victim|escape|escape:'url'}' class='attack-again'>Attack another {$display_name|escape}</a>
	</div>
