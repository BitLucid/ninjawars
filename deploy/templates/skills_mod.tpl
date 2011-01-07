<h1>Skill Effect</h1>

{if $attack_error}
<div class='ninja-notice'>{$attack_error}</div>
{else}

<div class='usage-mod-result'>

	{if $display_sight_table}
	
	<style type='text/css'>
	{literal}
	table#sight-info {
		width:100%;
		text-align:center;
		font-size:1.3em;
		margin-bottom:2em;	
	}
	
	#sight-info thead {
		border:none;
		border-bottom: 2px dashed rgb(100, 30, 30);
		font-weight:bold;
	}
	
	#sight-info td, #sight-info thead th{
		padding: .2em;
		font-size: 1.1em;
		text-align:center;
	}
	#sight-info td {
		text-shadow:black 2px 2px 2px;
		padding:.7em;
	}
	
	#sight-info td:hover {
		background-color:rgb(80, 30, 30);
	}
	{/literal}
	</style>
   		
   		
  <table id='sight-info' style='width:100%;'>
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
	{assign var="charName" value='<strong class="char-name">':$charName:'</strong>'}

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
  <p>You have received the $bounty gold bounty on $target's head for your deeds!</p>
	{/if}

	{if $suicided}
  <p>You have comitted suicide!</p>
	{/if}

	{if $destealthed}
  <p>Your actions have revealed you. You are no longer stealthed.</p>
	{/if}

{include file="defender_health.tpl" health=$target_ending_health health_percent=$target_ending_health_percent target_name=$target_name}

</div> {* End of usage mod result div*}

	{if $reuse}
<div class="skillReload">
	<a class='link-as-button' href="skills_mod.php?command={$command|escape:'url'}{if $target_id}&amp;target={$target_id|escape:'url'}{/if}">
		Use {$command} again
	</a>
</div>
	{/if}
{/if}{* End of there was no attack-error block. *}

<div class="LinkBack">
  Return to
{if $return_to_target}
  <a class='return-to-location' href='player.php?player_id={$target_id|escape}'>Ninja Detail</a>
{else}
  <a class='return-to-location' href="skills.php">Skills</a>
{/if}
</div>
