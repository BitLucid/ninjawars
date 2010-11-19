<h1>{$location|escape}</h1>

{if $error eq 1}
<p>No such ninja to put bounty on.</p>
{elseif $error eq 2}
<div>You do not have that much gold.</div>
{elseif $error eq 3}
<div>You did not offer a valid amount of gold.</div>
{elseif $error eq 4}
<div>The bounty on {$target|escape} may go no higher.</div>
{elseif $error eq 5}
The Doshin ignore your ill-funded attempt to bribe them.
{/if}

{if $amount_in neq $amount}
The doshin will only accept {$amount|escape} gold towards {$target|escape}'s bounty.<br>
{/if}

{if $success}
<div class='ninja-notice'>You have offered {$amount|escape} gold towards bringing {$target|escape} to justice.</div>
{/if}


<div class="description">
{foreach from=$description item="line"}
  <p>
    {$line}{* Unescaped *}
  </p>
{/foreach}
</div>

{if $myBounty gt 0}
<form id="bribe_form" action="doshin_office.php" method="post" name="bribe_form" style="width:40%;float:left;padding-right: 40px;">
  Bribe down your own bounty: <input id="bribe" type="text" size="4" maxlength="6" name="bribe" class="textField">
  <input id="command" type="submit" value="Bribe" name="command" class="formButton">
</form>
{/if}

<form action="" style="float:left;width:45%;">
  Put <input type="text" name="amount" value="{$amount|escape}" size="4" class="textField"> bounty on: <input type="text" name="target" value="{$target|escape}" class="textField">
  <input id="submit-bounty" type="submit" value="Offer Bounty" name="command" class="formButton">
</form>

{if count($data) gt 0}
<p style="clear:both;text-align:center;margin-top:8em;">Total Wanted Ninja: {$data|@count}</p>
<hr>

<table class="playerTable">
  <tr class='playerTableHead'>
    <th>
      Name
    </th>
    <th>
      Bounty
    </th>
    <th>
      Level
    </th>
    <th>
      Class
    </th>
    <th>
      Clan
    </th>
  </tr>
	{foreach from=$data item="row"}
  <tr class='playerRow'>
    <td class='playerCell'>
      <a href="player.php?player_id={$row.player_id|escape:'url'}">{$row.uname|escape}</a>
    </td>
    <td class='playerCell'>
      {$row.bounty|escape}
    </td>
    <td class='playerCell'>
      {$row.level|escape}
    </td>
    <td class='playerCell'>
      {$row.class|escape}
    </td>
    <td class='playerCell'>
		{if $row.clan_id}
      <a href="clan.php?command=view&amp;clan_id={$row.clan_id|escape:'url'}">{$row.clan_name|escape}</a>
		{/if}
    </td>
  </tr>
	{/foreach}
</table>
{else}
<p>The Doshin do not currently have any open bounties. Your village is safe.</p>
{/if}
