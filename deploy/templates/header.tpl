<!doctype html public "✰"> 
<!--[if lt IE 7]> <html lang="en-us" class="no-js ie6"> <![endif]--> 
<!--[if IE 7]>    <html lang="en-us" class="no-js ie7"> <![endif]--> 
<!--[if IE 8]>    <html lang="en-us" class="no-js ie8"> <![endif]--> 
<!--[if gt IE 8]><!--> <html lang="en-us" class="no-js"> <!--<![endif]-->
  <head>
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8">
    <meta name="keywords" content="ninjawars, ninja wars, ninjawars web game, ninjas, ninja weapons & techniques, samurai, free online games, {$title|escape}">
    <meta name="author" content="ninjawars.net">
    <meta name="description" content="Ninjawars: Battle other ninja for your survival.  Create a ninja and use skills or magic to kill samurai, the emperor's guards, or other ninja from rival clans.">
    <meta name="fb:page_id" property="fb:page_id" content="117346421617325" /><!-- Facebook tracker -->


    <title>{$title|escape} - Ninja Wars Web Game</title>
    <base href="{$smarty.const.WEB_ROOT}"><!--[if lte IE 6]></base><![endif]-->
    <!-- This css file now contains the mobile and print css files -->
    <link rel="stylesheet" type="text/css" href="css/style.css" media="Screen">
	<!-- Html5 shim for ie less than 9 -->
    <!--[if lt IE 9]>
		<script src="//html5shim.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->

{if $smarty.const.LOCAL_JS}
    <!-- Local jquery lib -->
    <script type="text/javascript" src="js/jquery-1.4.3.min.js"></script>
{else}
    <!-- Google jquery lib -->
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.3/jquery.min.js"></script>
{/if}
    <!-- Google Analytics moved to the footer.tpl -->
    <!-- All the global ninjawars javascript -->
    <script type="text/javascript" src="js/nw.js"></script>

    <script type="text/javascript">
	NW.loggedIn = {if $logged_in}true{else}false{/if};

{if $is_index}
{literal}
	var hash = window.location.hash;
	if(hash){ // If a hash exists.
		$(function(){
			var page = hash.substring(1); // Create a page from the hash by removing the #.
			$('iframe#main').attr('src', page); // Change the iframe src to use the hash page.
		});	
	}
{/literal}
{else}
	// Non index.
  {if $quickstat}
	{literal}
	$(document).ready(function() {{/literal}
		NW.refreshStats({$json_public_char_info});
		
	{literal}});{/literal}
  {/if}
	// Retrieve the current page/script name and put it into the hash for later refreshability.
	//var currentPage = '{$smarty.server.PHP_SELF}'.substring(1); // Take the /slash off the page name.
	{literal}
	//if(window.parent != window && currentPage != 'main.php'){
	//	window.parent.location.hash = currentPage; 
	//}
	{/literal}
{/if}

{literal}
// For all pages, if a link with a target of the main iframe is clicked
$(function(){
	$('html').removeClass('no-js');; // Remove no-js class when js present.

	$('a[target=main]').click(function(){
		var target = $(this).attr('href');
		var winToChange = window.parent != window? window.parent : window;
		winToChange.location.hash = target;
		// Then update the hash to the source for that link.
		return true;
	});
});
{/literal}
    </script>

{if $smarty.const.DEBUG}
    <link rel="stylesheet" type="text/css" href="css/debugger.css">
    <script type="text/javascript">
		NW.debugging = true;
    </script>
{/if}

  </head>
  <body class="{$body_classes|escape}">
{if !$is_index}
	<div id='solo-page-header'>
		<div id="logo-appended" style='position:absolute;top:0;left:0'>
		  <a href="/">
	        <img id='ninjawars-title-image' src='images/halfShuriken.png' title='Home' alt='Ninja Wars' width='108' height='108'>
		  </a>
		</div>
		<div id='logo-placeholder' style='width:110px;height:75px;display:inline-block;z-index:-1'>
		  <!-- Spacer div for the main shuriken linkback logo -->
		</div>
	{if !$logged_in}
		<span id='solo-page-login-link'><a href='login.php' class='link-as-button'>Log in</a></span> | <span><a href='signup.php' class='link-as-button'>Signup</a></span>
	{/if}
    </div>
{/if}{* End of check for index or quickstats to not display the appended logo for those *}
