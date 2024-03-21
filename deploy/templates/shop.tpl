<style>
{literal}
/* small screen media query */
/* Grade-A Mobile Browsers (Opera Mobile, Mobile Safari, Android Chrome) */
@media (max-device-width: 480px) and (max-device-width: 854px) {
  .shop figure {
    width:100%;
    text-align:center;
  }
}
.shop .shop-form{
  clear:both;
}

{/literal}
</style>

<h1>Shop</h1>
<nav>
	<a href="/map" class="return-to-location block">Return to the Village</a>
</nav>

<section class='shop'>
<!-- For google ad targetting -->
<!-- google_ad_section_start -->

  <figure class='float-left glassbox'>
    <img
        src='/images/scenes/weaponsmith_bald.png'
        width='500'
        class='img-fluid mx-auto d-block'
        alt='The weapons shop keeper' />
  </figure>

<div class='description glassbox' id='shop-description'>
  {foreach from=$shopSections item=$shopSection}
    <div class='shop-section'>
      {include file="shop.$shopSection.tpl"} {* buy | index *}
    </div>
  {/foreach}
</div>

<p class='glassbox'>
  Your gold: <span class='gold-count fade-in-slow'>石{$gold|number_format|escape}</span>
</p>

<form id="shop_form" class='shop-form' action="/shop/purchase" method="post" name="shop_form">
  <div class='text-centered slightly-padded'>
  {if $authenticated}
    <em class='speech'>How many of these items would you like?</em> <input id="quantity" type="number" min='1' max='99' name="quantity" class="textField">
  {else}
    <div class='fade-in-flash'>
      To purchase the items below you must <a href="/signup?referrer=ninjawars.net"><button class='btn btn-primary'>Start A New Game</button></a>.
    </div>
  {/if}
  </div>
  <div class='shop-list'>
    {include file='shop.items.tpl'}
  </div>
</form>

</section>

<!-- For google ad targetting -->
<!-- google_ad_section_end -->

<script>
var loggedIn = {if $authenticated}true{else}false{/if};
</script>

<script src="/js/shop.js"></script>

{if $show_ad}
{* Because this ad seems to keep breaking stuff *}

<div class='glassbox text-centered'>

<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-9488510237149880"
     crossorigin="anonymous"></script>
<!-- NWShopAd -->
<ins class="adsbygoogle"
     style="display:block"
     data-ad-client="ca-pub-9488510237149880"
     data-ad-slot="9729123112"
     data-ad-format="auto"
     data-full-width-responsive="true"></ins>
<script>
     (adsbygoogle = window.adsbygoogle || []).push({});
</script>

{/if}

</div>
