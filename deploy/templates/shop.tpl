<style>
{literal}
table.shop-list{
    margin-left:0;
    margin-right:0;
}
form{
    text-align:center;    
}
input.shopButton{
    width:17em;
    font-size:1.2em;
    padding:.1em;
    margin-bottom:.1em;
}
.shop-list td{
	font-size:1.1em;
}

{/literal}
</style>


<h1>Shop</h1>

<!-- For google ad targetting -->
<!-- google_ad_section_start -->

<div class='description' style='margin-bottom:.3em'>
{if $in_purchase}
    {if $not_enough_gold}
        <p><em class='speech'>The total comes to {$current_item_cost gold},</em> the shopkeeper tells you.</p>
        <p>Unfortunately, you do not have that much gold.</p>
    {else}
        <p class='obtained-item'>The shopkeeper hands over {$quantity} {$item}{$grammar}.</p>
        <p><em class='speech'>Will you be needing anything else today?</em> he asks you as he puts your gold in a safe.</p>
    {/if}
{else}
    <p>You enter the village shop and the shopkeeper greets you with a watchful eye.</p>
    <p>As you look over his wares he says, <em class='speech'>Don't try anythin' you'd regret.</em>, waves his hand at a giant tetsubo club hanging the wall, and grins at you.</p>
{/if}

</div>
<form id="shop_form" action="shop.php" method="post" name="shop_form" {if !$is_logged_in}onsubmit="return false;"{/if}>
<input id="purchase" type="hidden" value="1" name="purchase">

<table class='shop-list' style='border-collapse:separate; border-spacing:.3em;background-color:#333;width:90%;margin:.5em 5%'>

	<caption colspan='4' style='text-align:center;padding:.2em;font-size:1.3em;color:chocolate;'>
		A Shelf of Items
	</caption>
	<!--
	<thead>
	<tr>
	  <td>  Item  </td>  <td>  Description  </td>  <td>  Cost  </td>  <td>  Picture  </td>
	</tr>
	</thead>
	-->

	<tr>
	  <td colspan="4" style="text-align: center;padding: .3em;">
		{if $is_logged_in}
		  <em class='speech'>How many of those would you like?</em> <input id="quantity" type="text" size="3" maxlength="5" name="quantity" class="textField" value="{$quantity}">
		{else}
		  To purchase the items below you must <a href="signup.php?referrer=">become a ninja</a>.
		{/if}
	  </td>
	</tr>


	{foreach from=$item_costs item="item_info" key="item_internal_name"}
	<tr>
	  <td>
		<input name="item" type="submit" value="{$item_info.item_display_name|escape}" class="shopButton">
	  </td>

	  <td>
		({$item_info.usage})
	  </td>

	  <td class='gold'>
		${$item_info.item_cost}
	  </td>

	  <td>
		{if !$item_info.image}&nbsp;{else}<img style='max-height:25px;max-width:50px' src="images/items/{$item_info.image}" alt="{$item_info.item_display_name}">{/if}
	  </td>
	</tr>
	{/foreach}

	<tfoot>
		<tr>
		    <td colspan='4'>
		        &nbsp;
		    </td>
		</tr>
	</tfoot>

</table>
</form>

<!-- google_ad_section_end -->


<p class='gold-count'>
  Current gold: {$gold|escape}
<p>

<div style='margin:.1em auto;text-align:center'>

<!-- Google Ad -->
<script type="text/javascript"><!--
google_ad_client = "pub-9488510237149880";
/* 300x250, created 12/17/09 */
google_ad_slot = "9563671390";
google_ad_width = 300;
google_ad_height = 250;
//-->
</script>
<script type="text/javascript" src="http://pagead2.googlesyndication.com/pagead/show_ads.js"></script>

</div>
