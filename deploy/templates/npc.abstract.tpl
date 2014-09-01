{literal}
<style>
article#fight{
	font-size:150%;
}
article#fight nav{
	margin-top:1.5em;
	margin-left:8%;
	margin-right:10%;
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
.money{
    color:gold;
    color:rgba(255,215,0,.7);
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

	{if isset($npc_stats.short) && $npc_stats.short}
	<p>The {$display_name|escape} {$npc_stats.short}.</p>
	{/if}
	{if $is_quick}
	The {$race|escape} sees you and prepares to defend!
	{/if}
	{if $is_stronger}
	The {$race|escape} seems stronger than you!
	{/if}
	
	<p>The {$display_name|escape} wounds you for <span class='damage'>{$attack_damage} health</span>.</p>
	{if $display_statuses}
	<p>The {$display_name|escape}'s strike leaves you <span class='{$display_statuses_classes}'>{$display_statuses}</span>.</p>
	{/if}
	{if $survive_fight}
		{if $is_weaker}<p>The {if $is_villager}villager{/if}{if !$is_villager}{$race|escape}{/if} is no match for you!</p>{/if}
		{if $kill_npc}<p class='ninja-notice'>The {$display_name|escape} is defeated!</p>
			{if $added_bounty}
			<p class='bounty-notice'>{if $is_villager}You have slain a member of the village!{/if} A bounty of <em class='money'>{$added_bounty} gold</em> has been placed on your head!</p>
			{/if}
		{else}
			{if $is_weaker}
			<p class='target-escape'>The {$display_name|escape} flees from you and is able to escape!</p>
			{else}
			<p class='you-escape'>You are unable to kill the {$display_name|escape}, so you escape instead!</p>
			{/if}
		{/if}
		
		<section id='rewards'>
		{if $received_gold}<p>You gather <span class='gold'>{$received_gold} gold</span>.</p>{/if}
		{foreach from=$received_display_items item=display_item}<p>You obtained <span class='obtained-item'>{$display_item}</span>!</p>{/foreach}
		</section>
		
			
	{else}
		<div class='ninja-error'>The {$display_name|escape} has killed you!</div>
	{/if}

	</div>
	<nav>
		<a href='npc.php?victim={$victim|escape|escape:'url'}' class='attack-again'>Attack another {$display_name|escape}</a>
	</nav>
  </article>
