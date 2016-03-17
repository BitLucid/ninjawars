<style>
#inventory-content{
  margin-bottom:3em;
}
#inventory-content .item-list{
  margin-bottom:2em;margin-left:auto;margin-right:auto;width:100%;
}
#inventory-content .item-list .oddeven td{
  font-size:1em;padding-bottom:0.3em;
}
#inventory-content .item-list .oddeven .item-name{
  text-align: right;padding-right:1em;
}
#inventory-content .inventory-actions{
  margin-left:1em;margin-right:1em;
}
#inventory-content .item-count{
  text-align:left;
}
#inventory-content .usage{
  color:gray;cursor:help;
}
#inventory-content .item-icon{
  max-width:1.5em;max-height:1.5em;
}
</style>


<section id='inventory-content'>
  <h1>Your Inventory</h1>

{if $inventory}

  <div class='inventory-actions'>

    <p class='gold-count'>
      Current gold: {$gold_display|escape}
    <p>

    <small class='de-em'>Click a linked item to use it on yourself.</small>

    <table class='item-list'>
    	{foreach from=$inventory item="item_info" key="item_name"}
    		{if $item_info.count gt 0}
      <tr class='oddeven'>
        <td class='item-name'>
    			{if $item_info.self_use}
          <a title='Use a {$item_info.item_display_name|escape}' class='btn btn-primary' href="/item/self_use/{$item_info.item_id|escape:'url'|escape}">
    			{/if}
          {$item_info.display|escape}{if $item_info.image} <img class='item-icon' src='/images/items/{$item_info.image}'>{/if}
    			{if $item_info.self_use}
          </a>
    			{/if}
        </td>
        <td class='usage-cell'>{if $item_info.usage}<span class='usage' title='{$item_info.usage}'>&#8505;</span>{else}&nbsp;{/if}</td>
        <td class='item-count'>{$item_info.count|number_format:0|escape}</td>
      </tr>
    		{/if}
    	{/foreach}
    </table>

    <form id="player_search" action="/list" method="get" name="player_search">
      <div>
        Use an Item on a ninja?
        <input id="searched" type="text" maxlength="50" name="searched" class="textField">
        <input id="hide" type="hidden" name="hide" value="dead">
        <input type="submit" value="Search for Ninja" class="formButton">
      </div>
    </form>

  <div>

{else}
    <div class=''>You have no items, to buy some, visit the <a href="/shop">shop</a> or kill <a href='/enemies' target='main'>things</a>.</div>


  <p class='gold-count'>
  Current gold: {$gold_display|escape}
  <p>
{/if}

</section>
