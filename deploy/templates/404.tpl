<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
  <head>
    <title>
      Ninja Wars - 404 - Page Not Found
    </title>
    <base href="{$smarty.const.WEB_ROOT}"><!--[if lte IE 6]></base><![endif]-->
    <link rel="stylesheet" href="css/style.css">
    <style>
    #404-search{
      margin-left: 2em;
    }
    .child{
      display:inline-block;
    }
    .parent{
      text-align:center;
    }
    .left-aligned{
      text-align:left;
    }
    </style>
  </head>
  <body id="page-404">
  	<h1>NinjaWars :: 404 Page Not Found</h1>
    <div class='parent'>
      <div class='glassbox child'>
      	<img src='images/NinjaMeditationSilhouette_200.png' alt=''>
        <p>
          Pool of still water;
        </p>
        <p>
          within are 404 coins;
        </p>
        <p>
          it seems you are lost?
        </p>
        <!-- Haiku-ish?  The english syllable-centric version that misses the point, but close enough, just for fun -->
      </div>
    </div>
    <div class='parent'>
      <div class='glassbox child left-aligned'>
        <div class='thick'>Return to <a href="/">Ninjawars.net</a></div>
        <form action="http://www.google.com/search" name="searchbox" method="get" id='404-search'> 
          <input type="hidden" name="hl" value="en"> 
          <input type="hidden" name="ie" value="ISO-8859-1"> 
          <input type="hidden" name="sitesearch" value="ninjawars.net"> 
          <input maxlength="256" size="40" name="q" value=""> 
          <input type="submit" value="search the ninjawars site" name="btnG" style="font-size:75%;"> 
        </form>
        <div id='support-email'>or email <a href="mailto:{$smarty.const.SUPPORT_EMAIL}">{$smarty.const.SUPPORT_EMAIL}</a></div>
      </div>
    </div>
  </body>
</html>
