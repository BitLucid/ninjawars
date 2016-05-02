<style>
.float-right{
	float:right;clear:both;
}
.headed{
	border-top:1px brown solid;border-left:1px brown solid;
}
.char-inventory{
	height:1.3em;
}
.char-info-header{
	border-bottom:1px brown solid;color:#ADD8E6;
}
#admin-actions table caption{
	text-align:left;padding-left:10%;
}
.npc-box.tiled{
	display:inline-block; max-width:50em;vertical-align:top;
}
.npc-box.tiled h2{
	width:100%;margin:0;padding:0;transform:none;
}
.npc-box .npc-icon{
	max-width:48em;height:5em;
}
.npc-box figcaption{
	color:gray;text-overflow:ellipsis;overflow:hidden;white-space:nowrap;max-width:10em;width:100%;
}
.npc-box .char-profile{
	text-overflow:ellipsis;overflow:hidden;white-space:nowrap;max-width:10em;width:100%;
}
.npc-box dl strong{
	color:teal;
}
nav.admin-nav > div{
	background-color:rgba(129, 45, 12, 0.5);padding:0.5em 2em;
}
nav.admin-nav a{
	display:inline-block;margin-left:2em;
}
#duplicate-ips .ip{
	font-family:monospace;color:#C2E;
}
#admin-actions .account-info time{
	color:gray;
}
#admin-actions .half-width{
	width:49%;vertical-align:top;
}

</style>

<div id='admin-actions'>

<h1>Admin Actions</h1>

<nav class='admin-nav parent'>
	<div class='child'>
		<a class='btn btn-info' href='/ninjamaster/#npc-list'>Npc List</a>
		<a class='btn btn-info' href='/ninjamaster/#char-list'>Character List</a>
		<a class='btn btn-info' href='/ninjamaster/tools'>Validation Tools</a>
		<a class='btn btn-info' href='/ninjamaster/player_tags'>Character Tag List</a>
	</div>
</nav>

<section class='centered glassbox'>
	<form name='char-search' action='/ninjamaster' method='post'>
		View character @<input id='char-name' name='char_name' type='text' placeholder='character' value='{$char_name}' required=required>
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
	<h2>Viewing {$char_info.uname|escape}</h2>
	<div id='view-public' class='float-right'>
		<a href='/player?player_id={$char_info.player_id|escape}'>public view</a>
	</div>
	<table id='char-info-table'>
		<caption>Specific Character's info for <strong class='char-name'>{$char_info.uname|escape}</strong></caption>
		<thead>
			{foreach from=$char_info key='name' item='stat'}<th class='char-info-header'>{$name}</th>{/foreach}
		</thead>
		<tr>
		{foreach from=$char_info key='name' item='stat'}
			<td>{$stat}</td>
		{/foreach}
		</tr>
	</table>
	{if $char_info.first}
	<div class='char-profile'>Out-of-Character profile: {$first_message}</div>
	<div class='char-description'>Char Description: {$first_description}</div>
	{if $first_account}
	<section class='account-info inline-block half-width centered'>
		<div class='inline-block left-aligned'>
		<h3>Account Info</h3>
		<dl>
			<dt>Account Identity</dt><dd>{$first_account->identity()}</dd>
			<dt>Active Email</dt><dd>{$first_account->getActiveEmail()}</dd>
			<dt>Karma Total</dt><dd>{$first_account->getKarmaTotal()}</dd>
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
					<td class='headed'>{$column}</td><td> {$data}</td>
				{/foreach}
				</tr>
			{/foreach}
		</table>
	</section>
</section>
{/foreach}
{/if}

<div id='char-list'>
	{foreach from=$stats item='stat' key='stat_name'}
	<h2>Most {$stat_name}:</h2>
	<div class='glassbox'>
	{foreach from=$stat item='char'}
		<a href='/ninjamaster/?view={$char.player_id}' class='char-name'>{$char.uname|escape}</a> :: {$char.$stat_name}<br>
	{/foreach}
	</div>
	{/foreach}
</div>


{if $dupes}
<div id='duplicate-ips' class='glassbox'>
	<h3>Duplicate Ips</h3>
	{foreach from=$dupes item='dupe'}
	<a href='/ninjamaster/?view={$dupe.player_id}' class='char-name'>{$dupe.uname|escape}</a> :: IP <strong class='ip'>{$dupe.last_ip}</strong> :: days {$dupe.days}<br>
	{/foreach}
{/if}

<section class='special-info'>
	<h1 id='npc-list'>Npc list raw info</h1>
	<div class='npc-raw-info'>
			{foreach from=$npcs item='npc'}
		<div class='npc-box tiled'>
		  <h2>{$npc->identity()}</h2>
		  <figure>
		  	<img {if $npc->image()}src='/images/characters/{$npc->image()}'{/if} class='npc-icon' alt='no-image'>
		  	<figcaption>{$npc->shortDesc()}&nbsp;</figcaption>
		  </figure>
		  <dl>
			<dt>Name</dt><dd>{$npc->name()}</dd>
			<dt>Race</dt><dd>{$npc->race()}</dd>
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
		  	<figcaption>{$npc->shortDesc()}&nbsp;</figcaption>
		  </figure>
		  <dl>
			<dt>Name</dt><dd>{$npc->name()}</dd>
			<dt>Race</dt><dd>{$npc->race()}</dd>
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
	</div>
</section>


</div><!-- End of #admin-actions -->
