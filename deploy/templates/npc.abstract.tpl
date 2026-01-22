<link href="/css/npcs.css" rel="stylesheet" type="text/css" />

  <article id='fight' class='encounter-overall'>
	<h2>{$display_name|escape}</h2>

	<section class='npc-fight'>
	{if $image_path}
		<figure class='npc-avatar'>
		  <img 
		  	src='{cachebust file=$image_path}'
		    alt='A {$race}' 
			title='A {$race}'
			style='max-width: 450px;'
			>
		</figure>
	{/if}

	{if isset($npc_stats.short) && $npc_stats.short}
	<p>The {$display_name|escape} {$npc_stats.short}.</p>
	{/if}
	{if $is_quick or $npco->hasTrait('defender')}
	The {$race|escape} sees you and prepares to defend!
	{/if}
	{if $much_stronger}
	The {$race|escape} seems stronger than you!
	{/if}

	<p class='npc-combat-area'>
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

	<p class='pc-npc-combat-area'>
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
		{if $kill_npc}<p class='ninja-notice fade-in'>You kill the {$display_name|escape}!</p>
			{if $added_bounty}
				<div class='bounty-notice'>
					{if $is_villager}<p>You have slain a member of the village!</p>{/if} 
					<em class='money'>{$added_bounty}</em> bounty added.
				</div>
			{/if}
		{else}
			{if $is_weaker}
			<p class='ninja-notice target-escape'>The {$display_name|escape} flees from you and escapes!</p>
			{else}
				{if $much_stronger}
				<p class='you-escape' title='They had {$enemy_strength|escape} strength'>You are unable to defeat the {$display_name|escape}, so you escape instead!</p>
				{else}
				 <p title='They had {$npc_health} health'>You fight to a standstill and neither wins.</p>
				{/if}
			{/if}
			{if $tagline}
				<p><em>{$tagline}</em></p>
			{/if}

		{/if}

		<section id='rewards'>
		{if $received_gold}<p>You gather <span class='gold'>{$received_gold} gold</span>.</p>{/if}
		{foreach from=$received_display_items item=display_item}
			<p>You obtained <span class='obtained-item'>{$display_item}</span>!</p>
		{/foreach}
		&nbsp;
		</section>


	{else}
		<div class='ninja-error fade-in-flash'>The {$display_name|escape} has killed you!</div>
	{/if}

	</section><!-- end of .npc-fight -->
	<nav>
		<a class='btn btn-primary attack-again {if !$survive_fight}thick{/if}' href='/npc/attack/{$victim|escape|escape:'url'}'>Attack another {$display_name|escape}</a>
	</nav>
  </article>
