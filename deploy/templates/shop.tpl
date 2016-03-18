<style>
{literal}
table.shop-list {
	border-collapse: separate;
	border-spacing: .3em;
	background-color: #333;
	width: 90%;
	margin: .5em 5%;
}

form {
    text-align: center;
}

input.shopButton {
    width: 17em;
    font-size: 1.2em;
    padding: .1em;
    margin-bottom: .1em;
}

.shop-list td {
	font-size: 1.1em;
}

#shop-description {
	margin-bottom: 0.3em;
}

.shop-list .item-desc {
	color: #808177;
}
{/literal}
</style>
<script>
{literal}
$(document).ready(function() {
    $("#quantity").val(NW.storage.appState.get("quantity", 1));

    $("#shop_form").submit(function() {
{/literal}
        {if !$is_logged_in}return false;{/if}
{literal}
        NW.storage.appState.set("quantity", $("#quantity").val());
        return true;
    });
});
{/literal}
</script>

<h1>Weapons Shop</h1>

<!-- For google ad targetting -->
<!-- google_ad_section_start -->

<div class='description' id='shop-description'>
{include file="shop.$view_part.tpl"}
</div>

<form id="shop_form" action="/shop/purchase" method="post" name="shop_form">
	<table class='shop-list'>
		<caption colspan='4' class='text-centered slightly-padded accent'>
			A Shelf of Items
		</caption>

		<tr>
		  <td colspan="4" class='text-centered slightly-padded'>
			{if $is_logged_in}
			  <em class='speech'>How many of these would you like?</em> <input id="quantity" type="number" min='1' max='99' name="quantity" class="textField">
			{else}
			  To purchase the items below you must <a href="/signup?referrer=">become a ninja</a>.
			{/if}
		  </td>
		</tr>


		{foreach from=$item_costs item="item_info" key="item_internal_name"}
		<tr>
		  <td>
			<input name="item" id='item-{$item_internal_name}' type="submit" value="{$item_info.item_display_name|escape}" class="shopButton">
		  </td>

		  <td>
			<label for='item-{$item_internal_name}'><small class='item-desc'>{$item_info.usage}</small></label>
		  </td>

		  <td class='gold'>
			<label for='item-{$item_internal_name}'>石{$item_info.item_cost}</label>
		  </td>

		  <td>
			{if !$item_info.image}&nbsp;{else}<label for='item-{$item_internal_name}'><img style='max-height:25px;max-width:50px' src="images/items/{$item_info.image}" alt="{$item_info.item_display_name}"></label>{/if}
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

<p class='glassbox'>
	Your current gold: <span class='gold-count'>石{$gold|number_format|escape}</span>
<p>

<nav>
	<a href="/map" class="return-to-location block">Return to the Village</a>
</nav>

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
