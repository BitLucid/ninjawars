<h1>Item Use</h1>

{if $error eq 1}
<div class='ninja-error centered'>{$attack_error}</div>
{elseif $error eq 2}
You didn't choose an item/victim.
{elseif $error eq 3}
You do not have {$article|escape} {$itemName|escape}
{else}
<div class='usage-mod-result'>
	{assign var="charName" value='<strong class="char-name">':$target:'</strong>'}
  <p>
    {$alternateResultMessage|replace:'__TARGET__':$charName}
  </p>
  <p>
    {$resultMessage|replace:'__TARGET__':$target}
  </p>

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
		{include file='defender_health.tpl' health=$targetHealth health_percent=$targetHealthPercent target_name=$target}
	{/if}

	{if $suicide}
You have comitted suicide!<br>
	{/if}

	{if $repeat}
<br><a class='central-location' href="inventory_mod.php?item={$itemType|escape:'url'|escape}&amp;target_id={$target_id|escape:'url'|escape}&amp;link_back={$return_to|escape:'url'|escape}{if $selfTarget}&amp;selfTarget=1{/if}">Use {$itemName|escape} again?</a><br>

	{/if}
</div>
{/if}

<p>
  Return to
{if $return_to eq 'player'}
  <a href="player.php?player_id={$target_id|escape:'url'}" class='return-to-location'>Ninja Detail</a>
{elseif $return_to eq 'inventory'}
  <a href="inventory.php" class='return-to-location'>Inventory</a>
{else}
  <a href='combat.php' class='return-to-location'>Combat</a>
{/if}
</p>
