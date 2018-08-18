<!DOCTYPE html>
<html lang="en-us">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="keywords" content="ninjawars, ninja wars, the ninja game, ninjawars web game, ninjas, ninja weapons &amp; techniques, samurai, free online games, {$title|escape}">
    <meta name="author" content="ninjawars.net">
    <meta name="description" content="Ninjawars: The ninja game where you battle other ninja for your survival.  Create a ninja and use skills or magic to kill samurai, the emperor's guards, or other ninja from rival clans. {$title|escape} ">
    <meta name="fb:page_id" property="fb:page_id" content="117346421617325" /><!-- Facebook tracker -->

    <title>{$title|escape} - The Ninja Wars Ninja Game</title>

    <!-- Bootstrap core CSS -->
    <link href="/css/bootstrap.min.css" rel="stylesheet">
    <link href="/css/style.css" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.1.1/css/all.css" integrity="sha384-O8whS3fhG2OnA5Kas0Y9l3cfpmYjapjI0E4theH4iuMD+pLhbf6JI0jIMfYcK3yZ" crossorigin="anonymous">

    <!-- Custom styles for this template -->
    <link href="/css/sticky-footer-navbar.css" rel="stylesheet">

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Google jquery CDN version, sync composer component for tests if changed -->
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
{if $smarty.const.LOCAL_JS}
    <!-- Local js turned on for when CDN is unavailable -->
    <script>window.jQuery || document.write('<script src="{cachebust file="/js/jquery.min.js"}"><\/script>')</script>
{/if}
    <script src="/js/bootstrap.min.js"></script>
    <!-- Plugins go here -->
    <script type='text/javascript' src='{cachebust file="/js/jquery.timeago.js"}'></script>

{if $smarty.const.DEBUG}
    {literal}
    <script type="text/javascript">
        var NW = window.NW || {};
		NW.debugging = true;
    </script>
    {/literal}
{/if}

    <!-- All the global ninjawars javascript -->
    <script type="text/javascript" src="{cachebust file="/js/nw.js"}"></script>
{if $is_index}
    <script type="text/javascript" src="{cachebust file="/js/chat.js"}"></script>
{/if}

  </head>
  <body {if $body_classes}class='{$body_classes}'{/if}>
{if $is_index}{* Only display appended logo on solo pages *}
{else}
{/if}{* End of solo-page check *}
