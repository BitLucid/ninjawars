<h1>Item Use</h1>

{if $error eq 1}
<div class='ninja-error centered'>{$attack_error}</div>
{elseif $error eq 2}
You didn't choose an item/victim.
{elseif $error eq 3}
You do not have {$article|escape} {$itemName|escape}
{else}
<div class='usage-mod-result'>
{$alternateResultMessage}
{$resultMessage}

	{if $kill}
You have killed {$target|escape} with {$article|escape} {$itemName|escape}!<br>
You receive {$loot|escape} gold from {$target|escape}.<br>
	{/if}

	{if $bountyMessage}
<p>
  {$bountyMessage}
</p>
	{/if}

	{if $item_used}
<br>Removing {$itemName|escape} from your inventory.<br>
	{/if}

	{if $stealthLost}
Your actions have revealed you. You are no longer stealthed.<br>
	{/if}

	{if not $selfTarget}
		{include file='defender_health.tpl' health=$targetHealth health_percent=$targetHealthPercent target_name=$targetName}
	{/if}

	{if $suicide}
You have comitted suicide!<br>
	{/if}

	{if $repeat}
<br><a href="inventory_mod.php?item={$itemType|escape:'url'|escape}&amp;target_id={$target_id|escape:'url'|escape}&amp;link_back={$return_to|escape:'url'|escape}{if $selfTarget}&amp;selfTarget=1{/if}">Use {$itemName|escape} again?</a><br>
	{/if}
</div>
{/if}

<p>
Return to
{if $link_back}
	{$link_back}
{else}
	<a href='combat.php'>Combat</a>
{/if}
</p>
