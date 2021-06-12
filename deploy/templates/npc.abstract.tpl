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
.damage-amount{
	background-color:rgba(130, 0, 0, .5);
	border-radius:0.5rem;
	display:inline-block;
	padding:0 .3em;
}
.damage.miss{
	color: #006100;
}
.damage.nick{
	color:#ffe1ad;
}
.damage.wound{
	color:#dd164f;
}
.damage.savage{
	color:red;
	background-color:#2f2b2b;
}
.damage.obliterate{
	color:white;
	background-color:#800040;
	font-weight:bold;
}
.damage.kill{
	color:black;
	background-color:#cd124d;
	font-weight:bold;
}
</style>
{/literal}


  <article id='fight'>
	<h2>{$display_name|escape}</h2>

	<section class='npc-fight'>
	{if $image_path}
		<figure class='npc-avatar'>
		  <img src='{cachebust file=$image_path}' alt='A {$race}'>
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

	<p>
		{if $attack_damage > 0}
			{if $npco->hasTrait('horned')}
			<p>The {$display_name|escape}'s horns gore you.</p>
			{/if}
		{/if}
		The {$display_name|escape} 
		{if $attack_damage > 0}
			<span class='damage {$npc_damage_class}'>{$npc_damage_class}s you</span> 
			for <span class='damage-amount {$npc_damage_class}'>{$attack_damage} health</span>.
		{else}
			<span class='damage miss'>misses</span> you.
		{/if}
	</p>

	<p>
		You
		{if $ninja_damage > 0}
			<span class='damage {$ninja_damage_class}'>{$ninja_damage_class} the {$display_name|escape}</span> 
			with <span class='damage-amount {$ninja_damage_class}'>{$ninja_damage} damage</span>.
		{else}
			<span class='damage miss'>miss</span> the {$display_name|escape}.
		{/if}
	</p>

	{if $display_statuses}
	<p>The {$display_name|escape}'s strike leaves you <span class='{$display_statuses_classes}'>{$display_statuses}</span>.</p>
	{/if}
	{if $survive_fight}
		{if $is_weaker}<p>The {if $is_villager}villager{/if}{if !$is_villager}{$race|escape}{/if} is no match for you!</p>{/if}
		{if $kill_npc}<p class='ninja-notice'>You kill the {$display_name|escape}!</p>
			{if $added_bounty}
			<div class='bounty-notice'>{if $is_villager}<p>You have slain a member of the village!</p>{/if} <em class='money'>{$added_bounty}</div>
			{/if}
		{else}
			{if $is_weaker}
			<p class='ninja-notice target-escape'>The {$display_name|escape} flees from you and escapes!</p>
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
		<a class='btn btn-primary attack-again' href='/npc/attack/{$victim|escape|escape:'url'}'>Attack another {$display_name|escape}</a>
	</nav>
  </article>