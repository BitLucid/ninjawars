<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8">
	<meta name="keywords" content="ninjawars, ninja wars, ninjas, samurai, free online games, {$title}">
	<meta name="author" content="ninjawars.net">
	<meta name="description" content="Ninjawars: Battle other ninja for your survival.  Create a ninja and use skills or magic to kill samurai, the emperor's guards, or other ninja from rival clans.">

	<title>Ninja Wars: {$title}</title>

    <link rel="stylesheet" type="text/css" href="{$WEB_ROOT}css/style.css">
	<!--[if lte IE 6]>
    <link rel="stylesheet" type="text/css" href="{$WEB_ROOT}css/ie-6.css">
	<![endif]-->
	<!-- [if gte IE 7]>
	<link rel="stylesheet" type="text/css" href="{$WEB_ROOT}css/ie.css">
	<![endif]-->

	{if $local_js}
	<!-- Local jquery lib -->
	<script type="text/javascript" src="{$WEB_ROOT}js/jquery-1.3.2.min.js"></script>
	<script type="text/javascript" src="{$WEB_ROOT}js/debug.js"></script>
	{else}
	<!-- Google jquery lib -->
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js"></script>
	<!-- Google Analytics -->
	<script type="text/javascript" src='http://www.google-analytics.com/ga.js'></script>
	<!-- The google-analytics code that gets run is in nw.js -->
    {/if}
    <!-- All the global ninjawars javascript -->
	<script type="text/javascript" src="{$WEB_ROOT}js/nw.js"></script>
		{if $logged_in}
	<script type="text/javascript">
	<!--
		setLoggedIn();
	// -->
	</script>
	{else}
	<script type="text/javascript">
	<!--
		clearLoggedIn();
	// -->
	</script>
	{/if}
</head>
<body class='{$body_classes}'>
{if !$is_index}
<a href='/' id='logo-appended'>
	<img src='images/ninjawarslogo_75px.png' alt='NinjaWars'>
</a>
{/if}
