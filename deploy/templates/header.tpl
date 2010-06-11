{if !$section_only}
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
  <head>
	{if $templatelite.server.SCRIPT_NAME eq '/village.php' or $templatelite.server.SCRIPT_NAME eq '/mini_chat.php'}
    <noscript>
      <meta http-equiv="refresh" content="30">
    </noscript>
	{/if}
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8">
    <meta name="keywords" content="ninjawars, ninja wars, ninjas, ninja weapons & techniques, samurai, free online games, {$title|escape}">
    <meta name="author" content="ninjawars.net">
    <meta name="description" content="Ninjawars: Battle other ninja for your survival.  Create a ninja and use skills or magic to kill samurai, the emperor's guards, or other ninja from rival clans.">

    <title>Ninja Wars: {$title|escape}</title>
    <base href="{$templatelite.const.WEB_ROOT}"><!--[if lte IE 6]></base><![endif]-->
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <!--[if lte IE 6]>
    <link rel="stylesheet" type="text/css" href="css/ie-6.css">
    <![endif]-->
    <!-- [if gte IE 7]>
    <link rel="stylesheet" type="text/css" href="css/ie.css">
    <![endif]-->

    <!-- older IE assistive javascript -->
    <!--[if lt IE 8]>
    <script src="http://ie7-js.googlecode.com/svn/version/2.0(beta3)/IE8.js" type="text/javascript"></script>
    <![endif]-->
    
{if $templatelite.const.LOCAL_JS}
    <!-- Local jquery lib -->
    <script type="text/javascript" src="js/jquery-1.3.2.min.js"></script>
    <script type="text/javascript" src="js/debug.js"></script>
{else}
    <!-- Google jquery lib -->
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js"></script>
    <!-- Google Analytics -->
    <script type="text/javascript" src="http://www.google-analytics.com/ga.js"></script>
    <!-- The google-analytics code that gets run is in nw.js -->
{/if}
    <!-- All the global ninjawars javascript -->
    <script type="text/javascript" src="js/nw.js"></script>

    <script type="text/javascript">
    <!--
{if $logged_in}
		setLoggedIn();
{else}
		clearLoggedIn();
{/if}
    // -->
    </script>

{if $templatelite.const.DEBUG}
    <link rel="stylesheet" type="text/css" href="css/debugger.css">
    <script type="text/javascript">
    <!--
		NW.debug = 1;
    // -->
    </script>
{/if}
  </head>
  <body class="{$body_classes|escape}">
	{if !$is_index and $templatelite.server.SCRIPT_NAME neq '/quickstats.php'}
    <div id="logo-appended">
      <a href="/"><img src="images/ninjawarslogo_75px.png" alt="NinjaWars"></a>
    </div>
	{/if}
{/if}
