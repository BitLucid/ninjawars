<!DOCTYPE html> 
<!--[if lt IE 7]> <html lang="en-us" class="no-js ie6"> <![endif]--> 
<!--[if IE 7]>    <html lang="en-us" class="no-js ie7"> <![endif]--> 
<!--[if IE 8]>    <html lang="en-us" class="no-js ie8"> <![endif]--> 
<!--[if gt IE 8]><!--> <html lang="en-us" class="no-js"> <!--<![endif]-->
  <head>
    <meta charset="UTF-8">
    <meta name="keywords" content="ninjawars, ninja wars, the ninja game, ninjawars web game, ninjas, ninja weapons &amp; techniques, samurai, free online games, {$title|escape}">
    <meta name="author" content="ninjawars.net">
    <meta name="description" content="Ninjawars: The ninja game where you battle other ninja for your survival.  Create a ninja and use skills or magic to kill samurai, the emperor's guards, or other ninja from rival clans. {$title|escape} ">
    <meta name='viewport' content='width=device-width'>
    <meta name="fb:page_id" property="fb:page_id" content="117346421617325" /><!-- Facebook tracker -->


    <title>{$title|escape} - The Ninja Wars Ninja Game</title>
    <base href="{$smarty.const.WEB_ROOT}"><!--[if lte IE 6]></base><![endif]-->
    <!-- This css file now contains the mobile and print css files -->
    <link rel="stylesheet" type="text/css" href="css/style.css" media="Screen">


	<!-- Html5 shim for ie less than 9 -->
    <!--[if lt IE 9]>
		<script src="//html5shim.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->

{if !$smarty.const.LOCAL_JS}
    <!-- Google jquery lib -->
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
{/if}
	<script>window.jQuery || document.write('<script src="js/jquery-2.1.1.min.js"><\/script>')</script>
    <!-- Plugins go here -->
    <script type='text/javascript' src='/js/jquery.timeago.js'></script>

{if $smarty.const.DEBUG}
    <link rel="stylesheet" type="text/css" href="css/debugger.css">
    {literal}
    <script type="text/javascript">
        var NW = window.NW || {};
		NW.debugging = true;
    </script>
    {/literal}
{/if}

    <!-- All the global ninjawars javascript -->
    <script type="text/javascript" src="js/nw.js"></script>

{if $is_index}
    <script type="text/javascript" src="js/chat.js"></script>
{/if}
<style>
#logo-appended{
    position:absolute;top:0;left:0;
}
#logo-placeholder{
    width:110px;height:75px;display:inline-block;z-index:-1;
}
</style>

  </head>
  <body class="{$body_classes|escape}">
{if !$is_index}
	<div id='solo-page-header'>
		<div id="logo-appended">
		  <a href="/">
	        <img id='ninjawars-title-image' src='images/halfShuriken.png' title='Home' alt='Ninja Wars' width='108' height='108'>
		  </a>
		</div>
		<div id='logo-placeholder'>
		  <!-- Spacer div for the main shuriken linkback logo -->
		</div>
	{if !$logged_in}
		<span id='solo-page-login-link'><a href='login.php' class='link-as-button'>Log in</a></span> | <span id='solo-page-signup-link'><a href='signup.php' class='link-as-button'>Signup</a></span>
	{/if}
    </div>
{/if}{* End of check for index or quickstats to not display the appended logo for those *}
