<!DOCTYPE html>
<html lang="en-us">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    {* The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags *}
    <meta name="keywords" content="ninjawars, ninja wars, the ninja game, ninjawars web game, ninjas, ninja weapons &amp; techniques, samurai, free online games, {$title|escape}">
    <meta name="author" content="ninjawars.net">
    <meta name="description" content="Ninjawars: The ninja game where you battle other ninja for your survival.  Create a ninja and use skills or magic to kill samurai, the emperor's guards, or other ninja from rival clans. {$title|escape} ">
    <meta name="fb:page_id" property="fb:page_id" content="117346421617325" /><!-- Facebook tracker -->

    <title>{$title|escape} - The Ninja Wars Ninja Game</title>

    <!-- Bootstrap core CSS -->
    <link href="/css/bootstrap.min.css" rel="stylesheet">
    <link href="/css/style.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/9d4bd8fe2e.js" crossorigin="anonymous"></script>
    <link rel="manifest" href="/manifest.json">

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Google jquery CDN version, sync composer component for tests if changed -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
{if $smarty.const.LOCAL_JS}
    <!-- Local js turned on for when CDN is unavailable -->
    <script>window.jQuery || document.write('<script src="{cachebust file="/js/jquery.min.js"}"><\/script>')</script>
{/if}
    <script src="/js/bootstrap.min.js"></script>
    <!-- Plugins go here -->
    <script src='{cachebust file="/js/jquery.timeago.js"}'></script>


    <!-- Google Analytics, just add all the tracking info to an array at once -->
{literal}
    <!-- Google tag (gtag.js) -- Updated 1/9/2024 -- RR -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-WWN26L7SKM"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
    
      gtag('config', 'G-WWN26L7SKM');
    </script>
{/literal}

    <!--  Hotjar Tracking Code for ninjawars, 1/29/2024-->
{literal}
    <script>
        (function(h,o,t,j,a,r){
            h.hj=h.hj||function(){(h.hj.q=h.hj.q||[]).push(arguments)};
            h._hjSettings={hjid:3844866,hjsv:6};
            a=o.getElementsByTagName('head')[0];
            r=o.createElement('script');r.async=1;
            r.src=t+h._hjSettings.hjid+j+h._hjSettings.hjsv;
            a.appendChild(r);
        })(window,document,'https://static.hotjar.com/c/hotjar-','.js?sv=');
    </script>
{/literal}

{if $smarty.const.DEBUG}
    {literal}
    <script type="text/javascript">
        var NW = window.NW || {};
		NW.debugging = true;
    </script>
    {/literal}
{/if}

    <!-- All the global ninjawars javascript -->
    <script src="{cachebust file="/js/nw.js"}"></script>
{if $is_index}
    <script src="{cachebust file="/js/chat.js"}"></script>
{/if}

<!--  Hotjar Tracking Code for ninjawars, 1/29/2024-->
{literal}
<script>
    (function(h,o,t,j,a,r){
        h.hj=h.hj||function(){(h.hj.q=h.hj.q||[]).push(arguments)};
        h._hjSettings={hjid:3844866,hjsv:6};
        a=o.getElementsByTagName('head')[0];
        r=o.createElement('script');r.async=1;
        r.src=t+h._hjSettings.hjid+j+h._hjSettings.hjsv;
        a.appendChild(r);
    })(window,document,'https://static.hotjar.com/c/hotjar-','.js?sv=');
</script>
{/literal}

  </head>
  <body {if $body_classes}class='{$body_classes}'{/if}>
{if $is_index}{* Only display appended logo on solo pages *}
{else}
{/if}{* End of solo-page check *}
