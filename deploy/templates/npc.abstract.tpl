{literal}
<style>
article#fight nav{
	margin-top:1.5em;
	margin-left:8%;
	margin-right:10%;
}
.obtained-item{
	font-style:italic;
}
#rewards p{
	display:inline-block;
	font-weight:bold;
}
#rewards p + p{
	margin-left:3em;
}
.damage{
	background-color:rgba(100, 0, 0, .5);
	border-radius:.5em;
	display:inline-block;
	padding:0 .3em;
	font-weight:bold;
}
</style>
{/literal}
	
	
  <article id='fight'>
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
	{if $is_perceptive}
	<p>The {$display_name|escape} sees you and prepares to defend!</p>
	{/if}
	
	<p>The {$display_name|escape} wounds you for <span class='damage'>{$attack_damage} health</span>.</p>
	{if $statuses}
	<p>The {$display_name|escape}'s strike leaves you <span class='{$statuses}'>{$statuses}</span>.</p>
	{/if}
	{if $victory}
		<p class='ninja-notice'>You defeat the {$display_name|escape}!</p>

		{if $added_bounty}
		<p class='bounty-notice'>A bounty of {$added_bounty} gold has been placed on your head!</p>
		{/if}
		
		<section id='rewards'>
		{if $received_gold}<p>You gather <span class='gold'>{$received_gold} gold</span>.</p>{/if}
		{if $received_item}<p>You obtained a <span class='obtained-item'>{$received_item}</span>!</p>{/if}
		</section>
			
	{else}
		<div class='ninja-error'>The {$display_name|escape} has killed you!</div>
	{/if}

	</div>
	<nav>
		<a href='npc.php?victim={$victim|escape|escape:'url'}' class='attack-again'>Attack another {$display_name|escape}</a>
	</nav>
  </article>
