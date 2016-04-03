<!DOCTYPE html>
<html lang="en-us" class="no-js">
  <head>
    <meta charset="UTF-8">
    <meta name="keywords" content="ninjawars, ninja wars, the ninja game, ninjawars web game, ninjas, ninja weapons &amp; techniques, samurai, free online games, {$title|escape}">
    <meta name="author" content="ninjawars.net">
    <meta name="description" content="Ninjawars: The ninja game where you battle other ninja for your survival.  Create a ninja and use skills or magic to kill samurai, the emperor's guards, or other ninja from rival clans. {$title|escape} ">
    <meta name='viewport' content='width=device-width'>
    <meta name="fb:page_id" property="fb:page_id" content="117346421617325" /><!-- Facebook tracker -->


    <title>{$title|escape} - The Ninja Wars Ninja Game</title>
    <link rel="stylesheet" type="text/css" href="{cachebust file="/css/font-awesome.min.css"}">
    <link rel="stylesheet" type="text/css" href="{cachebust file="/css/style.css"}" media="Screen">

	<!-- Html5 shim for ie less than 9 -->
    <!--[if lt IE 9]>
		<script src="//html5shim.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->

{if !$smarty.const.LOCAL_JS}
    <!-- Google jquery lib -->
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
{/if}
	<script>window.jQuery || document.write('<script src="{cachebust file="/js/jquery.min.js"}"><\/script>')</script>
    <!-- Plugins go here -->
    <script type='text/javascript' src='{cachebust file="/js/jquery.timeago.js"}'></script>

{if $smarty.const.DEBUG}
    <link rel="stylesheet" type="text/css" href="{cachebust file="/css/debugger.css"}">
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
  <body class="{$body_classes|escape}">
{if !$is_index}{* Only display appended logo on solo pages *}
	<div id='solo-page-header'>
		<div id="logo-appended">
		  <a href="/">
	        <img id='ninjawars-title-image' src='{cachebust file="/images/halfShuriken.png"}' title='Home' alt='Ninja Wars' width='108' height='108'>
		  </a>
		</div>
		<div id='logo-placeholder'>
		  <!-- Spacer div for the main shuriken linkback logo -->
		</div>
	{if !$logged_in}
		<a id='solo-page-login-link' href='/login' class='btn btn-vital'>Log in</a> <a id='solo-page-signup-link' href='/signup' class='btn btn-vital'>Signup</a>
	{/if}
    </div>
{/if}{* End of solo-page check *}
