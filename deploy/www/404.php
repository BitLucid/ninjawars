<!DOCTYPE html>
<!-- html5 doctype enforces most compliant standards, even though use of html5 features isn't practical quite yet -->
<html>
  <head>
    <title>
      404 Error Page Not Found
    </title>
    <base href="{$WEB_ROOT}">
    <!--[if lte IE 6]></base><![endif]-->
    <link rel="stylesheet" href="css/style.css">
  </head>
  <body id="page-404">
  	<h1>Page Not Found</h1>
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
<!--
    <p>
      We are in the middle of an update. We should be done by 2:30pm EST. Please try back at that time.
    </p>
-->
<form action="http://www.google.com/search" name="searchbox" method="get" style="margin-left: 2em;"> 
  <input type="hidden" name="hl" value="en"> 
  <input type="hidden" name="ie" value="ISO-8859-1"> 
  <input type="hidden" name="sitesearch" value="ninjawars.net"> 
  <input maxlength="256" size="40" name="q" value=""> 
  <input type="submit" value="search the ninjawars site" name="btnG" style="font-size:75%;"> 
</form>
    <div>Return to <a href="index.php">Ninjawars.net</a></div>
    <div id='support-email'>or email <a href="mailto:<?php echo SUPPORT_EMAIL ?>"><?php echo SUPPORT_EMAIL ?></a></div>
  </body>
</html>
