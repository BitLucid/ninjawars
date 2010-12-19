<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
  <head>
  	{* // Commented out because it's kinda just annoying when js is turned off.
	{if $templatelite.server.SCRIPT_NAME eq '/village.php' or $templatelite.server.SCRIPT_NAME eq '/mini_chat.php'}
    <noscript>
      <meta http-equiv="refresh" content="90">
    </noscript>
	{/if}
	*}
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8">
    <meta name="keywords" content="ninjawars, ninja wars, ninjas, ninja weapons & techniques, samurai, free online games, {$title|escape}">
    <meta name="author" content="ninjawars.net">
    <meta name="description" content="Ninjawars: Battle other ninja for your survival.  Create a ninja and use skills or magic to kill samurai, the emperor's guards, or other ninja from rival clans.">

    <title>Ninja Wars: {$title|escape}</title>
    <base href="{$templatelite.const.WEB_ROOT}"><!--[if lte IE 6]></base><![endif]-->
    <link rel="stylesheet" type="text/css" href="css/style.css" media="Screen">
	<link rel="stylesheet" href="css/mobile.css" type="text/css" media="handheld">
    <!--[if lte IE 6]>
    <link rel="stylesheet" type="text/css" href="css/ie-6.css">
    <![endif]-->
    <!-- [if gte IE 7]>
    <link rel="stylesheet" type="text/css" href="css/ie.css">
    <![endif]-->

{if $templatelite.const.LOCAL_JS}
    <!-- Local jquery lib -->
    <script type="text/javascript" src="js/jquery-1.4.3.min.js"></script>
{else}
    <!-- Google Analytics -->
    <script type="text/javascript" src="http://www.google-analytics.com/ga.js"></script>
    <!-- The google-analytics code that gets run is in nw.js -->
    <script type="text/javascript">
    // GOOGLE ANALYTICS
    /* There's a script include that goes with this, but I just put it in the head directly.*/
{literal}
    try {
        var pageTracker = _gat._getTracker("UA-707264-2");
        pageTracker._trackPageview();
    } catch(err) {}
{/literal}
    </script>

    <!-- Google jquery lib -->
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.3/jquery.min.js"></script>
{/if}
    <!-- All the global ninjawars javascript -->
    <script type="text/javascript" src="js/nw.js"></script>

    <script type="text/javascript">
	NW.loggedIn = {if $logged_in}true{else}false{/if};
    </script>

{if $templatelite.const.DEBUG}
    <link rel="stylesheet" type="text/css" href="css/debugger.css">
    <script type="text/javascript">
		NW.debugging = true;
    </script>
{/if}
{if $quickstat and not $is_index}
    <script type="text/javascript">
	{literal}$(document).ready(function() {{/literal}
		NW.refreshStats({$json_public_char_info});
	{literal}});{/literal}
    </script>
{/if}
  </head>
  <body class="{$body_classes|escape}">
{if !$is_index}
	<div id='solo-page-header'>
		<div id="logo-appended" style='position:absolute;top:0;left:0'>
		  <a href="/">
	        <img id='ninjawars-title-image' src='images/halfShuriken.png' alt='Ninja Wars' width='108' height='108'>
		  </a>
		</div>
		<div id='logo-placeholder' style='width:110px;height:110px;display:inline-block;z-index:-1'>
		  <!-- Spacer div for the main shuriken linkback logo -->
		</div>
{if !$logged_in}
		<span id='solo-page-login-link'><a href='login.php' class='link-as-button'>Log in</a></span> | <span><a href='signup.php' class='link-as-button'>Signup</a></span>
{/if}
    </div>
{/if}{* End of check for index or quickstats to not display the appended logo for those *}
