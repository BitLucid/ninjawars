<h1 role='heading'>Map</h1>

<div id='attack-player-page'>

    {include file='nodes.tpl' nodes=$nodes}

    <nav class='centered thick' style='width:85vh;min-width:60rem'>
        <a href='/enemies' class='start-fight btn btn-default btn-lg btn-primary btn-block'>
            <i class='fas fa-bolt'></i> Fight!
        </a>
    </nav>

    {if $show_ad eq 1}
        <!-- This particular ad is here mainly to focus the targeting of the advertising to more nw related topics. -->

        <hr>

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

</div><!-- End of attack-player page container div -->
