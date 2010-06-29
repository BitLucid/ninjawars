<h1>Your Inventory</h1>

<div class="item-list">

{if $inventory}
<div style="margin-bottom: 1em;">Click a linked item to use it on yourself.</div>
<table style="width: 25em;height:10em;margin-bottom:2em;">
	{foreach from=$inventory item="item_info" key="item_name"}
		{if $item_info.count gt 0}
  <tr>
    <td style="font-size:1em;padding-bottom:.3em;text-align: right;padding-right:32%">
			{if isset($item_info.codename)}
      <a href="inventory_mod.php?item={$item_info.codename|escape:'url'|escape}&amp;selfTarget=1&amp;target={$username|escape:'url'|escape}&amp;link_back=inventory">
			{/if}
      {$item_info.display|escape}
			{if isset($item_info.codename)}
      </a>
			{/if}
    </td>
    <td style="font-size:1em;padding-bottom:.3em">{$item_info.count|escape}</td>
  </tr>
		{/if}
	{/foreach}
</table>
{else}
    You have no items, to buy some, visit the <a href="shop.php">shop</a>.
{/if}


</div>
  <form id="player_search" action="list_all_players.php" method="get" name="player_search">
    <div>
      <a href="list_all_players.php?hide=dead">Use an Item on a ninja?</a>
      <input id="searched" type="text" maxlength="50" name="searched" class="textField">
      <input id="hide" type="hidden" name="hide" value="dead">
      <input type="submit" value="Search for Ninja" class="formButton">
    </div>
  </form>

  <p>
  Current gold: {$gold|escape}
  <p>
