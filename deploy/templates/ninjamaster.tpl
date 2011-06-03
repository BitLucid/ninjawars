<h1>Admin Actions</h1>

{if $char_infos}
<!-- View the details of some ninja -->
{foreach from=$char_infos item='char_info'}
<table>
	<tr><thead>Specific Character's info for <strong class='char-name'>{$char_info.uname}</strong></thead></tr>
	<tr>
	{foreach from=$char_info key='name' item='stat'}
		<td style='border:1px brown solid'>{$name}</td><td> {$stat}</td>
	{/foreach}
	</tr>
</table>
<table>
	<tr><thead>Inventory for <strong class='char-name'>{$char_info.uname}</strong></thead></tr>
	{foreach from=$char_inventory key='name' item='item'}
		<tr style='height:1.3em'>
		<td>&#9734;</td>
		{foreach from=$item key='column' item='data'}
			<td style='border-top:1px brown solid;border-left:1px brown solid'>{$column}</td><td> {$data}</td>
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
