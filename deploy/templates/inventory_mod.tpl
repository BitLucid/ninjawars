{if $target}
    {assign var="charName" value=$target->name()|escape}
    {assign var="charName" value="<strong class=\"char-name\">$charName</strong>"}
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

<h1>Item Use</h1>

<nav class='LinkBack glassbox'>
    <a href="{$urlBack}" class='return-to-location'>{$linkText}</a>
</nav>

{if $error eq 1}
  <div class='centered glassbox'>
      <div class='ninja-error centered'>{$resultMessage}</div>
  </div>
{elseif $error eq 2}
  You didn't choose an item/victim.
{elseif $error eq 3}
You do not have {$article|escape} {$item->getName()|escape}
{else}
<div class='usage-mod-result'>
  <a href="/player?player_id={$target->id()|escape:'url'}">
      {include file="gravatar.tpl" gurl=$target->avatarUrl()}
  </a>
  {* This is kinda an abomination. *}
  {if $alternateResultMessage}
  <p class='alt-result-message'>
      {$alternateResultMessage|replace:'__TARGET__':$charName}
  </p>
  {/if}
  {if $resultMessage}
  <p class='result-message'>
      {$resultMessage|replace:'__TARGET__':$charName}
  </p>
  {/if}

  {if $target->health lte 0 and $target->id() eq $user_id}
    You have comitted suicide!<br>
  {else}
    You have killed {$target->name()|escape} with {$article|escape} {$item->getName()|escape}!<br>
    {if $loot}
    You receive {$loot|escape} gold from {$target->name()|escape}.<br>
    {/if}

    {if $bountyMessage}
    <p>
        {$bountyMessage}
    </p>
    {/if}
  {/if}

  {if $stealthLost}
    Your actions have revealed you. You are no longer stealthed.<br>
  {/if}

  {include file='defender_health.tpl' health=$target->health level=$target->level target_name=$target->name()}
  {if isset($self_use) and $self_use}
  <div class='centered'>
      {include file='self.current_turns.tpl' self=$target}
  </div>
  {/if}

  <nav class='attack-nav'>
      {if $repeat}
      <a class='attack-again thick btn btn-primary'
          href="/item/{$action}/{$item->getType()|escape:'url'|escape}/{$target->id()|escape:'url'|escape}/?link_back={$return_to|escape:'url'|escape}">
          Use {$item->getName()|escape} again?
      </a>
      <br>
      {/if}
      <a href='/enemies' class='return-to-location'>Return to the Fight</a>
  </nav>
  </div>
{/if}
