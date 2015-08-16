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
	text-align:center;
}
.npc-box.tiled{
	display:inline-block; max-width:50em;
}
.npc-box.tiled h2{
	width:100%;margin:0;padding:0;transform:none;
}
.npc-box .npc-icon{
	max-width:48em;height:5em;
}
</style>

<div id='admin-actions'>

<h1>Admin Actions</h1>

<section class='centered glassbox'>
	<form action='' method='post'>
		View character @<input id='char-name' type='text' placeholder='character' value='{$char_name}' required=required>
		<div><input type='Submit' value='Match'></div>
	</form>
</section>

{if $char_infos}
<!-- View the details of some ninja -->
{foreach from=$char_infos item='char_info'}
<div id='clear' class='float-right block'>
	<a href='/ninjamaster'>Clear</a>
</div>
<table>
	<caption>Specific Character's info for <strong class='char-name'>{$char_info.uname|escape}</strong></caption>
	<thead>
		{foreach from=$char_info key='name' item='stat'}<th class='char-info-header'>{$name}</th>{/foreach}
	</thead>
	<tr>
	{foreach from=$char_info key='name' item='stat'}
		<td>{$stat}</td>
	{/foreach}
	</tr>
	<tfoot>
		<tr><td>Out-of-Character profile: {$message}</td></tr>
	</tfoot>
</table>
<table class='char-inventory'>
	<caption>Inventory for <strong class='char-name'>{$char_info.uname|escape}</strong>:</caption>
	{foreach from=$char_inventory key='name' item='item'}
		<tr class='info'>
		<td>&#9734;</td>
		{foreach from=$item key='column' item='data'}
			<td class='headed'>{$column}</td><td> {$data}</td>
		{/foreach}
		</tr>
	{/foreach}
</table>
{/foreach}
{/if}

{foreach from=$stats item='stat' key='stat_name'}
<h2>Most {$stat_name}:</h2>
<div>
{foreach from=$stat item='char'}
	<a href='/ninjamaster/?view={$char.player_id}' class='char-name'>{$char.uname|escape}</a> :: {$char.$stat_name}<br>
{/foreach}
</div>
{/foreach}


{if $dupes}
<div>
	<h3>Duplicate Ips</h3>
	{foreach from=$dupes item='dupe'}
	<a href='/ninjamaster/?view={$dupe.player_id}' class='char-name'>{$dupe.uname|escape}</a> :: IP {$dupe.ip}<br>
	{/foreach}
{/if}

<section class='special-info'>
	<h1>Npc list raw info</h1>
	<div class='npc-raw-info'>
			{foreach from=$npcs item='npc'}
		<div class='npc-box tiled'>
		  <h2>{$npc->identity()}</h2>
		  <figure>
		  	<img src='/images/characters/{$npc->image()}' class='npc-icon' alt='no-image'>
		  </figure>
		  <dl>
			<dt>Name</dt><dd>{$npc->name()}</dd>
			<dt>Identity</dt><dd>{$npc->identity()}</dd>
			<dt>Race</dt><dd>{$npc->race()}</dd>
			<dt>Difficulty</dt><dd>{$npc->difficulty()}</dd>
			<dt>Max Damage</dt><dd>{$npc->max_damage()}</dd>
			<dt>Max Health</dt><dd>{$npc->max_health()}</dd>
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
		  </figure>
		  <dl>
			<dt>Name</dt><dd>{$npc->name()}</dd>
			<dt>Identity</dt><dd>{$npc->identity()}</dd>
			<dt>Race</dt><dd>{$npc->race()}</dd>
			<dt>Difficulty</dt><dd>{$npc->difficulty()}</dd>
			<dt>Max Damage</dt><dd>{$npc->max_damage()}</dd>
			<dt>Max Health</dt><dd>{$npc->max_health()}</dd>
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