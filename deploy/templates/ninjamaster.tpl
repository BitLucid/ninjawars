{include file="ninjamaster.css.tpl"}

<span style='float:left'><a href='/'><button type='button' class='btn btn-default'><i class='fa fa-home'></i></button></a></span>
<div id='admin-actions'>

<h1>Admin Actions</h1>

{if $error}
	<div class='parent'>
		<div class='child error'>
			{$error|escape}
		</div>
	</div>
{/if}

<nav class='admin-nav parent'>
	<div class='child'>
		<a class='btn btn-info' href='/ninjamaster/#npc-list-stats'>Npc List</a>
		<a class='btn btn-info' href='/ninjamaster/#char-list'>Character List</a>
		<a class='btn btn-info' href='/ninjamaster/tools'>Validation Tools</a>
		<a class='btn btn-info' href='/ninjamaster/player_tags'>Character Tag List</a>
		<a class='btn btn-info' href='/ninjamaster/#item-list-area'>Item List</a>
	</div>
</nav>

<section class='centered glassbox'>
	<form name='char-search' action='/ninjamaster' method='post'>
		View character @<input id='char-name' name='char_name' type='text' placeholder='character' value='{$char_name|escape}' required=required>
		<div><input type='Submit' value='Find'></div>
	</form>
</section>

{if $char_infos}
<!-- View the details of some ninja -->
{foreach from=$char_infos item='char_info'}
<div id='clear' class='float-right block button-mimic glassbox'>
	<a href='/ninjamaster'>Clear</a>
</div>
<section class='char-info-area glassbox'>
	<h2><a href='?view={$char_info.player_id|escape}'>Viewing {$char_info.uname|escape}</a></h2>
	<div id='view-public' class='float-right'>
		<a href='/player?player_id={$char_info.player_id|escape}'>public view</a>
	</div>
	<table id='char-info-table'>
		<caption>Specific Character's info for <strong class='char-name'>{$char_info.uname|escape}</strong></caption>
		<thead>
			{foreach from=$char_info key='name' item='stat'}<th class='char-info-header'>{$name|escape}</th>{/foreach}
		</thead>
		<tr>
		{foreach from=$char_info key='name' item='stat'}
			<td>{$stat|escape}</td>
		{/foreach}
		</tr>
	</table>
	{if $char_info.active !== 1}
	<div class='alert alert-info'>
		This character is inactive.
	</div>
	{/if}
	{if $char_info.first}
	<div class='char-profile'>Out-of-Character profile: {$first_message|escape}</div>
	<div class='char-description'>Char Description: {$first_description|escape}</div>
	{* Action to deactivate a player*}
	<section class='half-width constrained'>
		<header><h3>Actions</h3></header>
		<details class='constrained'>
			<summary>SEE ACTIONS TO TAKE</summary>
			<div>
				<div id='deactivate-character'>
					<form method='post' action='' onSubmit='return confirmDeactivation(event, {$char_info.player_id}, afterDeactivation)'>
					<input type='hidden' name='deactivate' value='1'>
					<button title='Will make this character non-active' type='submit' class='btn btn-warning' name='submit'>
						Make {$char_info.uname|escape} Non-Operational
					</button>
					</form>
				</div>
			</div>
		</details>
	</section>
	{if $first_account}
	<section class='account-info inline-block half-width centered'>
		<div class='inline-block left-aligned'>
		<h3>Account Info</h3>
		<dl>
			<dt>Account Identity</dt><dd>{$first_account->identity()|escape}</dd>
			<dt>Active Email</dt><dd>{$first_account->getActiveEmail()|escape}</dd>
			<dt>Karma Total</dt><dd>{$first_account->getKarmaTotal()|escape}</dd>
			<dt>Last Login</dt><dd><time class='timeago' datetime='{$first_account->getLastLogin()|escape}'>{$first_account->getLastLogin()|escape}</time></dd>
			<dt>Last Login Failure</dt><dd><time class='timeago' datetime='{$first_account->getLastLoginFailure()|escape}'>{$first_account->getLastLoginFailure()|escape}</time></dd>
			<dt>Operational</dt><dd>{if $first_account->isOperational()}true{else}false{/if}</dd>
			<dt>Confirmed</dt><dd>{if $first_account->isConfirmed()}1{else}0{/if}</dd>
		</dl>
		</div>
	</section>
	{/if}
	{/if}
	<section class='char-inventory-area inline-block half-width'>
		<h3>Inventory for <strong class='char-name'>{$char_info.uname|escape}</strong></h3>
		<table class='char-inventory'>
			{foreach from=$char_inventory key='name' item='item'}
				<tr class='info'>
				<td>&#9734;</td>
				{foreach from=$item key='column' item='data'}
					<td class='headed'>{$column|escape}</td><td> {$data|escape}</td>
				{/foreach}
				</tr>
			{/foreach}
		</table>
	</section>
</section>
{/foreach}
{/if}

<section id='char-list'>
	<header>
		<h3>Char List of High Rollers</h3>
	</header>
	<div class='text-center'>
		<button class='btn btn-default show-hide-next' type='button'>Show/Hide</button>
	</div>
	<div id='char-list-stats'>
		{foreach from=$stats item='stat' key='stat_name'}
		<h2>Most {$stat_name}:</h2>
		<div class='glassbox'>
		{foreach from=$stat item='char'}
			<a href='/ninjamaster/?view={$char.player_id}' class='char-name'>{$char.uname|escape}</a> :: {$char.$stat_name|escape}<br>
		{/foreach}
		</div>
		{/foreach}
	</div>
</section>


{if $dupes}
<section id='duplicate-ips' class='glassbox'>
	<header><h3>Duplicate Ips</h3></header>
	<div class='text-center'>
		<button class='btn btn-default show-hide-next' type='button'>Show/Hide</button>
	</div>
	<div>
		{foreach from=$dupes item='dupe'}
		<a href='/ninjamaster/?view={$dupe.player_id|escape}' class='char-name'>{$dupe.uname|escape}</a> :: IP <strong class='ip'>{$dupe.last_ip|escape}</strong> :: days {$dupe.days|escape}<br>
		{/foreach}
	</div>
</section>
{/if}

<section class='special-info'>
	<header><h1 id='npc-list-stats'>Npc list raw info</h1></header>
	<div class='text-center'>
		<button class='btn btn-default show-hide-next' type='button'>Show/Hide</button>
	</div>
	<div class='npc-raw-info'>
			{foreach from=$npcs item='npc'}
		<div class='npc-box tiled'>
		  <h2>{$npc->identity()}</h2>
		  <figure>
		  	<img {if $npc->image()}src='/images/characters/{$npc->image()}'{/if} class='npc-icon' alt='no-image'>
		  	<figcaption>{$npc->shortDesc()|escape}&nbsp;</figcaption>
		  </figure>
		  <dl>
			<dt>Name</dt><dd>{$npc->name()|escape}</dd>
			<dt>Race</dt><dd>{$npc->race()|escape}</dd>
			<dt>Difficulty</dt><dd><strong>{$npc->difficulty()}</strong></dd>
			<dt>Max Damage</dt><dd>{$npc->maxDamage()}</dd>
			<dt>Max Health</dt><dd>{$npc->getMaxHealth()}</dd>
		</dl>
		<div>
			Traits: 
				{foreach from=$npc->traits() item='trait'}
				{$trait|escape}
				{/foreach}
		</div>
		</div>
			{/foreach}
		<h3>Unfinished Raw Npcs</h3>
			{foreach from=$trivial_npcs item='npc'}
		<div class='npc-box tiled'>
		  <h2>{$npc->identity()}</h2>
		  <figure>
		  	<img src='/images/characters/{$npc->image()}' class='npc-icon' alt='no-image'>
		  	<figcaption>{$npc->shortDesc()|escape}&nbsp;</figcaption>
		  </figure>
		  <dl>
			<dt>Name</dt><dd>{$npc->name()|escape}</dd>
			<dt>Race</dt><dd>{$npc->race()|escape}</dd>
			<dt>Difficulty</dt><dd><strong>{$npc->difficulty()|escape}</strong></dd>
			<dt>Max Damage</dt><dd>{$npc->maxDamage()|escape}</dd>
			<dt>Max Health</dt><dd>{$npc->getMaxHealth()|escape}</dd>
		</dl>
		<div>
			Traits: 
				{foreach from=$npc->traits() item='trait'}
				{$trait|escape}
				{/foreach}
		</div>
		</div>
			{/foreach}
	</div>
</section>

<section>
	<header><h1 id='item-list-area'>Item Raw Data</h1></header>
	<div class='text-center'>
		<button class='btn btn-default show-hide-next' type='button'>Show/Hide</button>
	</div>
{include file="ninjamaster.items.tpl"}
</section>

<script src='/js/ninjamaster.js'></script>

</div><!-- End of #admin-actions -->