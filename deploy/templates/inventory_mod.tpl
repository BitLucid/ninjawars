<h1>Item Use</h1>

{if $error eq 1}
<div class='ninja-error centered'>{$attack_error}</div>
{elseif $error eq 2}
You didn't choose an item/victim.
{elseif $error eq 3}
You do not have {$article|escape} {$itemName|escape}
{else}
<div class='usage-mod-result'>
  <a href="/player?player_id={$target_id|escape:'url'}">{include file="gravatar.tpl" gurl=$targetObj->avatarUrl()}</a>
	{assign var="charName" value=$target|escape}
	{assign var="charName" value="<strong class=\"char-name\">$charName</strong>"}
	{* This is kinda an abomination. *}
  {if $alternateResultMessage}
  <p>
    {$alternateResultMessage|replace:'__TARGET__':$charName}
  </p>
  {/if}
  {if $resultMessage}
  <p>
    {$resultMessage|replace:'__TARGET__':$charName}
  </p>
  {/if}

	{if $kill}
	You have killed {$target|escape} with {$article|escape} {$itemName|escape}!<br>
	You receive {$loot|escape} gold from {$target|escape}.<br>
	{/if}

	{if $bountyMessage}
	<p>
	  {$bountyMessage}
	</p>
	{/if}

	{if $stealthLost}
	Your actions have revealed you. You are no longer stealthed.<br>
	{/if}

	{if not $selfTarget}
		{include file='defender_health.tpl' health=$targetHealth level=$targetObj->level target_name=$target}
	{/if}

	{if $suicide}
You have comitted suicide!<br>
	{/if}

	{if $repeat}
<br>
	<a class='attack-again thick btn btn-primary' href="/item/use/{$itemType|escape:'url'|escape}/{$target_id|escape:'url'|escape}/?link_back={$return_to|escape:'url'|escape}{if $selfTarget}&amp;selfTarget=1{/if}">
		Use {$itemName|escape} again?
	</a>
	<br>

	{/if}
</div>
{/if}


<div class='LinkBack glassbox'>
  Return to
{if $return_to eq 'player'}
  <a href="/player?player_id={$target_id|escape:'url'}" class='return-to-location'>{if isset($charName)}view {$charName}{else}view ninja{/if}</a>
{elseif $return_to eq 'inventory'}
  <a href="/inventory" class='return-to-location'>Inventory</a>
{else}
  <a href='/enemies' class='return-to-location'>Combat</a>
{/if}
</div>
