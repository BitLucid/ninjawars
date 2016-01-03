<style>
#inventory-content{
  margin-bottom:3em;
}
#inventory-content .item-list{
  margin-bottom:2em;margin-left:auto;margin-right:auto;min-width:80%;
}
#inventory-content .item-list .oddeven td{
  font-size:1em;padding-bottom:0.3em;
}
#inventory-content .item-list .oddeven .item-name{
  text-align: right;padding-right:32%;
}
#inventory-content .inventory-actions{
  margin-left:1em;
}
</style>


<section id='inventory-content'>
  <h1>Your Inventory</h1>

{if $inventory}

  <div class='inventory-actions'>

    <small class='de-em'>Click a linked item to use it on yourself.</small>

    <table class='item-list'>
    	{foreach from=$inventory item="item_info" key="item_name"}
    		{if $item_info.count gt 0}
      <tr class='oddeven'>
        <td class='item-name'>
    			{if isset($item_info.self_use) && $item_info.self_use == 't'}
          <a href="inventory_mod.php?item={$item_info.item_id|escape:'url'|escape}&amp;selfTarget=1&amp;target_id={$char_id|escape:'url'|escape}&amp;link_back=inventory">
    			{/if}
          {$item_info.display|escape}
    			{if isset($item_info.self_use) && $item_info.self_use == 't'}
          </a>
    			{/if}
        </td>
        <td class='item-count'>{$item_info.count|escape}</td>
      </tr>
    		{/if}
    	{/foreach}
    </table>

    <p class='gold-count'>
    Current gold: {$gold_display|escape}
    <p>

    <form id="player_search" action="list.php" method="get" name="player_search">
      <div>
        <a href="list.php?hide=dead">Use an Item on a ninja?</a>
        <input id="searched" type="text" maxlength="50" name="searched" class="textField">
        <input id="hide" type="hidden" name="hide" value="dead">
        <input type="submit" value="Search for Ninja" class="formButton">
      </div>
    </form>

  <div>

{else}
    <div class=''>You have no items, to buy some, visit the <a href="shop.php">shop</a> or kill <a href='enemies.php' target='main'>things</a>.</div>
    

  <p class='gold-count'>
  Current gold: {$gold_display|escape}
  <p>
{/if}

</section>