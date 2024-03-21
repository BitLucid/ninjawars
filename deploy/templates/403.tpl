<!DOCTYPE html>
<html>
  <head>
    <title>
      Ninja Wars - 403 - Not Authorized
    </title>
    <link rel="stylesheet" href="{cachebust file="/css/style.css"}">
    <link rel="stylesheet" type="text/css" href="{cachebust file="/css/font-awesome.min.css"}">
    <meta name="robots" content="noindex">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
    <!-- <meta http-equiv="refresh" content="30"> -->
  </head>
  <body id="page-403">
    <h1>NinjaWars :: 403 - Not Authorized</h1>
    <section class='main parent'>
      <div class='child glassbox'>
        <img class='error-page-character' src='/images/characters/pixel_ninja_nope.jpg' alt=''>
        <h2 class='error'><i class="fa fa-meh-o"></i> {if $error}{$error|escape}{else}Sorry, 403: You don't seem to be authorized to access that page{/if}</h2>
        <div class='glassbox thick' style='clear:both'>
          <form action="https://www.google.com/search" name="searchbox" method="get" id='search-403'> 
            <input type="hidden" name="hl" value="en"> 
            <input type="hidden" name="ie" value="ISO-8859-1"> 
            <input type="hidden" name="sitesearch" value="ninjawars.net"> 
            <input maxlength="256" size="40" name="q" value=""> 
            <input type="submit" value="search the site" name="btnG"> 
          </form>
        </div>
      </div>
    </section>

    <footer class='parent'>
      <div class='glassbox child left-aligned'>
        <div class='thick'>Return to <a href="/">the Ninjawars.net homepage</a></div>
        <div id='support-email'>or <a href='/staff' target='main'>get help on the staff page</a></div>
      </div>
    </footer>
<script>
// Hide search box if within iframe to prevent crossorigin problems
if(typeof(this.parent) !== 'undefined' && this.parent !== this.window){
  document.getElementById('search-403').style.display='none';
}
</script>
  </body>
</html>
