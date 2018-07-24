<style>
#inventory-content{
  margin-bottom:3em;
}
#inventory-content .item-list{
  margin-bottom:2em;margin-left:auto;margin-right:auto;width:100%;max-width:30rem;
}
#inventory-content .item-list .oddeven td{
  font-size:1em;padding-bottom:0.3em;line-height:2.3;
}
#inventory-content .item-list .oddeven .item-name{
  padding-right:1em;
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
.footnote{
  background-color:rgba(0, 0, 0, 0.5);
}
</style>


<section id='inventory-content'>

  {include file="flash-message.tpl"}

  <h1>Your Inventory</h1>

{if $inventory}

  <div class='inventory-actions'>

    <p class='gold-count'>
      Current gold: 石{$gold_display|escape}
    <p>

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
      <tfoot>
        <tr>
          <td colspan=3 class='centered footnote'>
            <small class='de-em'>(Click a linked item to use it on yourself.)</small>
          </td>
        </tr>
      </tfoot>
    </table>

    <form id="player_search" action="/list" method="get" name="player_search">
      <div class='input-group'>
        <input id="searched" type="text" maxlength="50" name="searched" class="form-control textField">
        <input id="hide" type="hidden" name="hide" value="dead">
        <span class='input-group-btn'>
          <button class='btn btn-primary formButton' type="submit"><i class="fas fa-search"></i> to use items on a ninja</button>
        </span>
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
