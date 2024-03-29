{* older skills_mod.php template, now using SkillController.php *}
{assign var="charName" value=$targetObj->name()|escape}
{assign var="charName" value="<strong class=\"char-name\">$charName</strong>"}

<section>
	<h1>Skill Effect</h1>

<div class="LinkBack glassbox">
{if $return_to_target}
  <a href="/player?player_id={$targetObj->id()|escape:'url'}" class='return-to-location'>View {$charName}</a>
{else}
  <a class='return-to-location' href="/skill">Skills</a>
{/if}
</div>

{if $attack_error}
<div class='ninja-notice'>{$attack_error}</div>
{else}

<div class='usage-mod-result'>

	<div>
		<a href="/player?player_id={$targetObj->id()|escape:'url'}">{include file="gravatar.tpl" gurl=$targetObj->avatarUrl()}</a>
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



	{if $generic_skill_result_message}
  {$generic_skill_result_message|replace:'__TARGET__':$charName}
	{/if}

	{if $generic_state_change}
  {$generic_state_change|replace:'__TARGET__':$charName}
	{/if}

	{if $loot > 0}
  <p>You receive <span class='gold-count'>{$loot} gold</span> from {$targetObj->name()}.</p>
	{/if}

	{if $added_bounty > 0}
  <p>Your victim was weaker than you. The townsfolk are angered. A bounty of {$added_bounty} gold has been placed on your head!</p>
	{/if}

	{if $bounty > 0}
  <p>You have received the {$bounty} gold bounty on {$targetObj->name()}'s head for your deeds!</p>
	{/if}

	{if $suicided}
  <p>You have comitted suicide!</p>
	{/if}

	{if $destealthed}
  <p>Your actions have revealed you. You are no longer stealthed.</p>
	{/if}

{include file="defender_health.tpl" health=$targetObj->health level=$targetObj->level target_name=$targetObj->name()}
{if isset($self_use) and $self_use}
<div class='centered'>
	{include file='self.current_turns.tpl' self=$player}
</div>
{/if}

{if $turn_cost}
	<div id='turn-cost'> You used {$turn_cost} turn{if $turn_cost > 1}s{/if}.</div>
{/if}

{if $ki_cost}
	<div id='ki-cost'> You used {$ki_cost} ki.</div>
{/if}

</div> {* End of usage mod result div*}

{/if}{* End of there was no attack-error block. *}

	<nav class='attack-nav' style='padding-left:2rem'>
	{if $reuse && !$attack_error}
		<div>
			<a class='attack-again thick btn btn-primary' href="/skill/{if $self_use}self_{/if}use/{$act|escape:'url'}/{if $targetObj->id()}{$targetObj->id()|escape:'url'}/{/if}">
				Use {$act} again
			</a>
		</div>
	{/if}
		<a href='/enemies' class='btn btn-default return-to-location'>
			Return to the Fight
		</a>
	</nav>

</section>
