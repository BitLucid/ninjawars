{include file="ninjamaster.css.tpl"}

<span style='float:left'><a href='/'><button type='button' class='btn btn-default'><i class='fa fa-home'></i></button></a></span>
<div id='admin-actions'>

<h1>Admin Dashboard</h1>

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
<a class='btn btn-info' href='/epics'>UI Epics</a>
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
<section class='char-info-area glassbox'>
	<span class='char-actions float-right'>
		<a href='./' title='clear'><i class='fa fa-times-circle'></i></a>
		<a href='/player?player_id={$char_info.player_id|escape}' title='View public profile'><i class='fa fa-eye'></i></a>
	</span>
	<h2><a href='?view={$char_info.player_id|escape}'>{$char_info.uname|escape}</a></h2>
	<div id='char-info-scroll'>
		<table id='char-info-table'>
			<thead>
				{foreach from=$char_info key='name' item='stat'}<th class='char-info-header'>{$name|escape}</th>{/foreach}
			</thead>
			<tr>
			{foreach from=$char_info key='name' item='stat'}
				<td>{$stat|escape}</td>
			{/foreach}
			</tr>
		</table>
	</div>
	<div class='text-center'>
		<div class='highlight-box'>
		{$char_info.days} days
		</div>
	</div>
	{if $char_info.first}
	<div class='char-profile'>Out-of-Character profile: {$first_message|escape}</div>
	<div class='char-description'>Char Description: {$first_description|escape}</div>
	{if $first_account}
	<section class='account-info inline-block half-width centered'>
		<div>
			<h3>Account Info</h3>
			<dl class='left-aligned'>
				<dt>Account Identity</dt><dd>{$first_account->identity()|escape}</dd>
				<dt>Active Email</dt><dd>{$first_account->getActiveEmail()|escape}</dd>
				<dt>Karma Total</dt><dd>{$first_account->getKarmaTotal()|escape}</dd>
				<dt>Last Login</dt><dd><time class='timeago' datetime='{$first_account->getLastLogin()|escape}'>{$first_account->getLastLogin()|escape}</time></dd>
				<dt>Last Login Failure</dt><dd><time class='timeago' datetime='{$first_account->getLastLoginFailure()|escape}'>{$first_account->getLastLoginFailure()|escape}</time></dd>
				<dt>Operational</dt><dd>{if $first_account->isOperational()}true{else}false{/if}</dd>
				<dt>Confirmed</dt><dd>{if $first_account->isConfirmed()}1{else}0{/if}</dd>
                <dt>Created</dt><dd><time class="created-time timeago" datetime="{$first_account->created_date|escape}" title="{$first_account->created_date|escape}">
				{$first_account->created_date|escape}</time></dd>
			</dl>
		</div>
		<details class='constrained' style='margin-bottom:1.25rem'>
			<summary>SEE ACTIONS TO TAKE</summary>
			<div>
				<div>
				{if $char_info.active === 1}
					<button class='btn btn-warning' id='start-deactivate'>
						Begin to Deactivate {$char_info.uname|escape}
					</button>
					<button type='button' class='btn btn-danger' style='display:none' id='deactivate-character' data-char-id="{$char_info.player_id}">
						Are you sure you want to deactivate {$char_info.uname|escape}
					</button>
					{/if}
				</div>
				<div>
					{if $char_info.active !== 1}
					<button type='button' class='btn btn-default' id='reactivate-character' data-char-id="{$char_info.player_id}">
						Reactivate {$char_info.uname|escape}
					</button>
					{/if}
				</div>
			</div>
		</details>
		{if $char_info.active !== 1}
		<div class='alert alert-info'>
			This character is inactive.
		</div>
		{/if}
	</section>
	{/if}
	{/if}
	<section class='char-inventory-area inline-block half-width'>
		<h3>Inventory for <strong class='char-name'>{$char_info.uname|escape}</strong></h3>
		<details class='constrained'>
			<summary>
				See item counts
			</summary>
			<div>
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
			</div>
		</details>
	</section>
</section>
{/foreach}
{/if}


<section>
	<header>
		<h2 id='usage-usage'>Recent Usage</h2>
	</header>
	<div class='carded-area'>
		<div class='card'>
			<div class='card-container'>
				<h5>New Players</h5>
				<div>Recent new players in 7 days: {$usage.new_count}</div>
				<ul>
					{foreach from=$usage.new item='nChar'}
						<li><a href='?view={$nChar.player_id|escape}'>{$nChar.uname|escape}</a> <time class='timeago' datetime='{$nChar.created_date|escape}'>{$nChar.created_date|escape}</time></li>
					{/foreach}
				</ul>
			</div>
		</div>
		<div class='card'>
			<div class='card-container'>
				<h5>Logins</h5>
				<div>Recent logins in 7 days: {$usage.recent_count}</div>
				<ul>
					{foreach from=$usage.recent item='nChar'}
						<li><a href='?view={$nChar.player_id|escape}'>{$nChar.uname|escape}</a> <time class='timeago' datetime='{$nChar.last_login|escape}'>{$nChar.last_login|escape}</time></li>
					{/foreach}
				</ul>
			</div>
		</div>
	</div>
</section>

<section id='review-new-signups'>
	<header>
		<h3>Review New Signups</h3>
	</header>
	<div class='text-center'>
		<button class='btn btn-default show-hide-next' type='button'>Show/Hide</button>
	</div>
	<div id='review-new-signups'>
		<h5>New Signups</h5>
		<div>Recent new player accounts created: {$signups.new_count}</div>
		<div>
			<ul>
				{foreach from=$signups.new item='nsChar'}
					<li>
						<button type='button' class='btn btn-danger deactivate-character' data-char-id="{$nsChar.player_id}">
							Deactivate {$nsChar.uname|escape}
						</button>
						<a href='?view={$nsChar.player_id|escape}'>{$nsChar.uname|escape}</a> <time class='timeago' datetime='{$nsChar.created_date|escape}'>{$nsChar.created_date|escape}</time> 
					</li>
				{/foreach}
			</ul>
		</div>
	</div>
</section>

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
			<a href='/ninjamaster/?view={$char.player_id}' class='char-name'>{$char.uname|escape}</a> :: {$char.stat|escape}<br>
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
	<header><h2 id='npc-list-stats'>Npc list raw info</h2></header>
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
	<header><h2 id='item-list-area'>Item Raw Data</h2></header>
	<div class='text-center'>
		<button class='btn btn-default show-hide-next' type='button'>Show/Hide</button>
	</div>
{include file="ninjamaster.items.tpl"}
</section>

<script type='module' src='/js/ninjamaster.js'></script>

</div><!-- End of #admin-actions -->
