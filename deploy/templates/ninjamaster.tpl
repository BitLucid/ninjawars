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
	<caption>Specific Character's info for <strong class='char-name'>{$char_info.uname}</strong></caption>
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
	<caption>Inventory for <strong class='char-name'>{$char_info.uname}</strong>:</caption>
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
	<a href='/ninjamaster/?view={$char.player_id}' class='char-name'>{$char.uname}</a> :: {$char.$stat_name}<br>
{/foreach}
</div>
{/foreach}


{if $dupes}
<div>
	<h3>Duplicate Ips</h3>
	{foreach from=$dupes item='dupe'}
	<a href='/ninjamaster/?view={$dupe.player_id}' class='char-name'>{$dupe.uname}</a> :: IP {$dupe.ip}<br>
	{/foreach}
{/if}


</div><!-- End of #admin-actions -->