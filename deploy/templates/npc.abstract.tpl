{literal}
<style>
article#fight{
	font-size:110%;
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
	background-color:rgba(130, 0, 0, .5);
	border-radius:.5em;
	display:inline-block;
	padding:0 .3em;
	font-weight:bold;
}
.money{
    color:gold;
    color:rgba(255,215,0,.7);
}
.npc-fight{
	width:80%;margin:0 10%;
}
#fight .npc-avatar{
	float:left;margin:0.5em 1em 0.5em 0.5em;text-align:center;
}
</style>
{/literal}
	
	
  <article id='fight'>
	<h2>{$display_name|escape}</h2>
	
	<section class='npc-fight'>
	{if $image_path}
		<figure class='npc-avatar'>
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
		{if $kill_npc}<p class='ninja-notice'>You kill the {$display_name|escape}!</p>
			{if $added_bounty}
			<p class='bounty-notice'>{if $is_villager}You have slain a member of the village!{/if} A bounty of <em class='money'>{$added_bounty} gold</em> has been placed on your head!</p>
			{/if}
		{else}
			{if $is_weaker}
			<p class='ninja-notice target-escape'>The {$display_name|escape} flees from you and you let it live!</p>
			{else}
				{if $is_stronger}
				<p class='you-escape'>You are unable to kill the {$display_name|escape}, so you escape instead!</p>
				{else}
				 <p>&nbsp;</p>
				{/if}
			{/if}

		{/if}
		
		<section id='rewards'>
		{if $received_gold}<p>You gather <span class='gold'>{$received_gold} gold</span>.</p>{/if}
		{foreach from=$received_display_items item=display_item}<p>You obtained <span class='obtained-item'>{$display_item}</span>!</p>{/foreach}
		&nbsp;
		</section>
		
			
	{else}
		<div class='ninja-error'>The {$display_name|escape} has killed you!</div>
	{/if}

	</section><!-- end of .npc-fight -->
	<nav>
		<a href='npc.php?victim={$victim|escape|escape:'url'}' class='attack-again'>Attack another {$display_name|escape}</a>
	</nav>
  </article>
