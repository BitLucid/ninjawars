<h1>Weapons Shop</h1>

<section class='shop'>
<!-- For google ad targetting -->
<!-- google_ad_section_start -->

<div class='description' id='shop-description'>
  {include file="shop.$view_part.tpl"}
</div>

<p class='glassbox'>
  Your current gold: <span class='gold-count'>石{$gold|number_format|escape}</span>
</p>

<form id="shop_form" action="/shop/purchase" method="post" name="shop_form">
  <p class='text-centered slightly-padded'>
  {if $authenticated}
    <em class='speech'>How many of these items would you like?</em> <input id="quantity" type="number" min='1' max='99' name="quantity" class="textField">
  {else}
    To purchase the items below you must <a href="/signup?referrer=">become a ninja</a>.
  {/if}
  </p>
  <div class='shop-list'>
		{foreach from=$item_costs item="item_info" key="item_internal_name"}
    <div class='item-purchase-area'>
			<button name="item" id='item-{$item_internal_name}' type="submit" value="{$item_info.item_internal_name|escape}" class="btn btn-default btn-lg">{if $item_info.image}<img class='item-icon' src="/images/items/{$item_info.image}" alt="{$item_info.item_display_name}">{/if} {$item_info.item_display_name|escape}</button>
      <div class='gold'>
        <label for='item-{$item_internal_name}'>石{$item_info.item_cost}</label>
      </div>
      <p class='usage-text'>
        <small class='item-desc'>{$item_info.usage}</small>
      </p>
    </div>
		{/foreach}
  </div>
</form>

<nav>
	<a href="/map" class="return-to-location block">Return to the Village</a>
</nav>

</section>

<!-- For google ad targetting -->
<!-- google_ad_section_end -->

<script>
var loggedIn = {if $authenticated}true{else}false{/if};
</script>

<script src="/js/shop.js"></script>

<div class='glassbox text-centered'>

  <!-- Google Ad -->
  <script type="text/javascript"><!--
  google_ad_client = "pub-9488510237149880";
  /* 300x250, created 12/17/09 */
  google_ad_slot = "9563671390";
  google_ad_width = 300;
  google_ad_height = 250;
  //-->
  </script>
  <script type="text/javascript" src="https://pagead2.googlesyndication.com/pagead/show_ads.js"></script>

</div>
