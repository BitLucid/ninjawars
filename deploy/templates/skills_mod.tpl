
<section>
	<h1>Skill Effect</h1>

{if $attack_error}
<div class='ninja-notice'>{$attack_error}</div>
{else}

<div class='usage-mod-result'>

	<div>
		<a href="player.php?player_id={$target_id|escape:'url'}">{include file="gravatar.tpl" gurl=$target->avatarUrl()}</a>
	</div>

	{if $display_sight_table}
	  		
  <table id='sight-info' class='full-width'>
  	<thead>
		<tr>   
		{foreach from=$sight_data item="loop_data" key="loop_header"}
      <th>{$loop_header}</th>
		{/foreach}
		</tr>
	</thead>
	<tbody>
		<tr>
		{foreach from=$sight_data item="loop_data" key="loop_header"}
		<td>{$loop_data}</td>
		{/foreach}
		</tr>
	</tbody>
  </table>
	{/if}

	{assign var="charName" value=$target|escape}
	{assign var="charName" value="<strong class=\"char-name\">$charName</strong>"}

	{if $generic_skill_result_message}
  {$generic_skill_result_message|replace:'__TARGET__':$charName}
	{/if}

	{if $generic_state_change}
  {$generic_state_change|replace:'__TARGET__':$charName}
	{/if}

	{if $killed_target}
  <p>You have killed {$charName} with {$command}!</p>
	{/if}
	{if $loot > 0}
  <p>You receive <span class='gold-count'>{$loot} gold</span> from {$target}.</p>
	{/if}

	{if $added_bounty > 0}
  <p>Your victim was much weaker than you. The townsfolk are angered. A bounty of {$added_bounty * 25} gold has been placed on your head!</p>
	{/if}

	{if $bounty > 0}
  <p>You have received the {$bounty} gold bounty on {$target}'s head for your deeds!</p>
	{/if}

	{if $suicided}
  <p>You have comitted suicide!</p>
	{/if}

	{if $destealthed}
  <p>Your actions have revealed you. You are no longer stealthed.</p>
	{/if}

{include file="defender_health.tpl" health=$target_ending_health health_percent=$target_ending_health_percent target_name=$target_name}

{if $turn_cost}
	<div id='turn-cost'> You used {$turn_cost} turn{if $turn_cost > 1}s{/if}.</div>
{/if}

{if $ki_cost}
	<div id='ki-cost'> You used {$ki_cost} ki.</div>
{/if}

	{if $reuse}
<div class="skillReload glassbox">
	<a class='attack-again thick btn btn-primary' href="skills_mod.php?command={$command|escape:'url'}{if $target_id}&amp;target={$target_id|escape:'url'}{/if}">
		Use {$command} again
	</a>
</div>
	{/if}

</div> {* End of usage mod result div*}

{/if}{* End of there was no attack-error block. *}

</section>

<div class="LinkBack glassbox">
  Return to
{if $return_to_target}
  <a href="player.php?player_id={$target_id|escape:'url'}" class='return-to-location'>view {$charName}</a>
{else}
  <a class='return-to-location' href="skills.php">Skills</a>
{/if}
</div>
