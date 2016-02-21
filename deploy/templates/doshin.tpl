{if $location eq 0}
	{assign var="locationLabel" value="Doshin Office"}
{elseif $location eq 1}
	{assign var="locationLabel" value="Behind the Doshin Office"}
{elseif $location eq 2}
	{assign var="locationLabel" value="The Rat-infested Alley behind the Doshin Office"}
{/if}

<h1>{$locationLabel}</h1>

{if $error}
  <div class='parent'>
  {if $error eq 1}
  <div class='ninja-error child'>No such ninja to put bounty on.</div>
  {elseif $error eq 2}
  <div class='ninja-error child'>You do not have that much gold.</div>
  {elseif $error eq 3}
  <div class='ninja-error child'>You did not offer a valid amount of gold.</div>
  {elseif $error eq 4}
  <div class='ninja-notice child'>The bounty on {$target|escape} may go no higher.</div>
  {elseif $error eq 5}
  <div class='ninja-notice child'>The Doshin ignore your ill-funded attempt to bribe them.</div>
  {elseif $error eq 6}
  <div class='ninja-notice child'>You cannot put a bounty on yourself.</div>
  {/if}
  </div>
{/if}

{if $command eq 'offer'}
	{if $amount_in neq $amount}
  <div class='parent'>
	 <div class='ninja-error child'>The doshin will only accept {$amount|escape} gold towards {$target|escape}'s bounty.</div>
  </div>
	{/if}

	{if $success}
	<div class='ninja-notice'>You have offered {$amount|escape} gold towards bringing {$target|escape} to justice.</div>
	{/if}
{/if}


<div class="description{if $myBounty gt 0} clean{/if}">
{if $location eq 0}
  <p>
    You walk up to the Doshin Office to find the door locked. The Doshin are busy protecting the borders of the village from thieves.
  </p>
  <p>
      Nearby on a wall is a notice that <span class='speech'>Commoners found carring katana will be imprisoned.</span> Nailed to the door is an official roster of wanted criminals and the bounties offered for their heads.
  </p>
  <p>
    A few men that do seem to be associated with the doshin doze near the entrance. Every so often someone approaches and slips them something that clinks and jingles.
  </p>
{elseif $location eq 1}
  <p>
    <span class="speech">This black mist weather we have today makes it hard to see some things,</span> one of the Doshin tells you as he palms your gold. He then directs you out through a back alley.
  </p>
  <p>
    You find yourself in a dark alley. A rat scurries by. To your left lies the main street of the village.
  </p>
{elseif $location eq 2}
  <p>
    <span class="speech">Trying to steal from the Doshin, eh!</span> one of the men growls.
  </p>
  <p>
    Where before there were only relaxing men idly ignoring their duties there are now unsheathed katanas and glaring eyes.
  </p>
  <p>
    A group of the Doshin advance on you before you can escape and proceed to rough you up with fists and the hilts of their katana. Finally, they take most of your gold and toss you into the alley behind the building.
  </p>
  <p>
    Bruised and battered, you find yourself in a dark alley. A rat scurries by. To your left lies the main street of the village.
  </p>
{/if}

</div>

<section class='bounty-related'>

{if $myBounty gt 0}
  <div class='parent thick'>
    <div class='ninja-info'>You have a {$myBounty|number_format:0|escape} gold bounty on your head.</div>
  </div>

<form id="bribe_form" action="/doshin/bribe" method="post" name="bribe_form" class='half-column'>
  Bribe down your own bounty: <input id="bribe" type="text" size="4" maxlength="6" name="bribe" class="textField" required="required">
  <input id="command" type="submit" value="Bribe" class="formButton">
</form>
{/if}

<form action="/doshin/offerBounty" class='half-column'>
  Offer <input type="text" name="amount" value="{$amount|default:''|escape}" size="4" class="textField" required="required"> bounty on: <input type="text" name="target" value="{$target|default:''|escape}" class="textField">
  <input id="submit-bounty" type="submit" value="Offer Bounty" class="formButton" required="required">
</form>

{if count($bounties) gt 0}

<table class="playerTable clear">
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
	{foreach from=$bounties item="row"}
  <tr class='playerRow'>
    <td class='playerCell'>
      <a href="/player?player_id={$row.player_id|escape:'url'}">{$row.uname|escape}</a>
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
      <a href="/clan/view?clan_id={$row.clan_id|escape:'url'}">{$row.clan_name|escape}</a>
		{/if}
    </td>
  </tr>
	{/foreach}
</table>
<div class='centered glassbox'>
  <small class='de-em'>Total Wanted Ninja: {$bounties|@count}</small>
</div>
{else}
<p>The Doshin do not currently have any open bounties. Your village is safe.</p>
{/if}

{if $display_gold}
<div class='gold thick'>
  You have {$display_gold} gold.
</div>
{/if}

</section><!-- End of bounty-related -->


<nav>
  <a href="/map" class="return-to-location block">Return to the Village</a>
</nav>
