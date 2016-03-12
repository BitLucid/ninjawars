<h1>Item Use</h1>

{if $target}
  {assign var="charName" value=$target->name()|escape}
  {assign var="charName" value="<strong class=\"char-name\">$charName</strong>"}
{/if}

{if $error eq 1}
<div class='ninja-error centered'>{$resultMessage}</div>
{elseif $error eq 2}
You didn't choose an item/victim.
{elseif $error eq 3}
You do not have {$article|escape} {$item->getName()|escape}
{else}
<div class='usage-mod-result'>
  <a href="/player?player_id={$target->id()|escape:'url'}">{include file="gravatar.tpl" gurl=$target->avatarUrl()}</a>
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

  {if $target->health() lte 0}
    {if $target->id() eq $user_id}
    You have comitted suicide!<br>
    {else}
	You have killed {$target->name()|escape} with {$article|escape} {$item->getName()|escape}!<br>
	You receive {$loot|escape} gold from {$target->name()|escape}.<br>

      {if $bountyMessage}
	<p>
	  {$bountyMessage}
	</p>
      {/if}
    {/if}
  {/if}

  {if $stealthLost}
	Your actions have revealed you. You are no longer stealthed.<br>
  {/if}

  {include file='defender_health.tpl' health=$target->health() level=$target->level target_name=$target->name()}

  {if $repeat}
  <br>
  <a class='attack-again thick btn btn-primary' href="/item/{$action}/{$item->getType()|escape:'url'|escape}/{$target->id()|escape:'url'|escape}/?link_back={$return_to|escape:'url'|escape}">
    Use {$item->getName()|escape} again?
  </a>
  <br>
  {/if}
</div>
{/if}

{if $return_to eq 'player' and $target}
  {assign var="targetId" value=$target->id()|escape:'url'}
  {assign var="urlBack" value="/player?player_id=$targetId"}
  {assign var="linkText" value="view $charName"}
{elseif $return_to eq 'inventory'}
  {assign var="urlBack" value="/inventory"}
  {assign var="linkText" value="Inventory"}
{else}
  {assign var="urlBack" value="/enemies"}
  {assign var="linkText" value="Combat"}
{/if}

<div class='LinkBack glassbox'>
  Return to
  <a href="{$urlBack}" class='return-to-location'>{$linkText}</a>
</div>
