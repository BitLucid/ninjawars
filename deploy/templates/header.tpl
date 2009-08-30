<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8">
	<meta name="keywords" content="ninjawars, ninja wars, ninja, samurai, free online game,
	free games, this here is not your mommas naruto game">
	<meta name="description" content="Ninjawars: battle other ninja for survival.">

	<title>{$title}</title>

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
<script type="text/javascript">
var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
</script>
<script type="text/javascript">
try {
var pageTracker = _gat._getTracker("UA-707264-2");
pageTracker._trackPageview();
} catch(err) {}</script>
    {/if}
    <!-- All the global ninjawars javascript -->
	<script type="text/javascript" src="{$WEB_ROOT}js/nw.js"></script>
</head>
<body class='{$body_classes}'>
