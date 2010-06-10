<h1>Your Inventory</h1>

<div class="item-list">

{if $items}
<div style="margin-bottom: 10px;">Click a linked item to use it on yourself.</div>
<table style="width: 250px;">
{foreach from=$items item="amount" key="item"}
	{assign var="data" value=$item_data[$item]}
	Data: {$item_data[$item]}
	{if $amount gt 0 and $data}
  <tr>
    <td style="line-height: 14px;">
		{if $data.codename}
      <a href="inventory_mod.php?item={$data.codename|escape:'url'|escape}&amp;selfTarget=1&amp;target={$username|escape:'url'|escape}&amp;link_back=inventory">
		{/if}
      {$data.display|escape}:
		{if $data.codename}
      </a>
		{/if}
    </td>
    <td>{$amount}</td>
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
  Current gold: {$gold}
  <p>
