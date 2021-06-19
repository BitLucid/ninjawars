    <!-- Version {$version} -->

  <link href="/css/splash.css" rel="stylesheet"/>


    <!-- Top horizontal bar -->
    <header id='index-header' class='clearfix'>
		<div id="logo-appended">
		  <a href="/">
	        <img id='ninjawars-title-image' src='{cachebust file="/images/halfShuriken.png"}' title='Home' alt='Ninja Wars' width='108' height='108'>
		  </a>
		</div>
		<div id='logo-placeholder'>
      &nbsp; <!-- Spacer div for the main shuriken linkback logo -->
		</div>
		<nav role='navigation' id='top-bar' class='navigation'>
		  <a id='solo-page-login-link' href='/login' class='btn btn-vital'>Log in</a> <a id='solo-page-signup-link' href='/signup' class='btn btn-vital'>Signup</a>
		</nav>
		<nav id='top-logo'>
      <h1>The Ninja Game at <a href='/intro' target='main'><img src='{cachebust file="/images/nw_bamboo_logo_soft.png"}' alt='NinjaWars' width='200' height='100'></a>.net</h1>
		</nav>

    <nav role='navigation' id='subcategory-bar' class='navigation'>
      <ul id='ninjas-subcategory'>
        <li><a href="/list" target="main">Ninjas</a></li>
        <li><a href="/clan" target="main">Clans</a></li>
      </ul>
      <ul id='map-subcategory'>
      	<li><a href='/map' id='menu-map-head' target='main' title='Travel to different locations on the map'>Map&rarr;</a></li>
        <li><a href="/shop" target="main">Shop</a></li>
        <li><a href="/work" target="main">Field</a></li>
        <li>
          <a href="/doshin" target="main">Doshin <img class='doshin-image' src="{cachebust file="/images/doshin.png"}" alt=""></a>
        </li>
      </ul>
    </nav> <!-- End of subcategory bar -->

	  </header><!-- End of header -->


      <div id='core' class='clearfix'>
      <!-- MAIN COLUMN STARTS HERE -->
		{include file="core.tpl"}

    {include file='aside.with-extras.tpl'}

    </div><!-- End of core -->

<!-- Version: {$version} -->

{literal}
<script>
if (top.location != location) { // Framebreak on the splash page to prevent any issues.
  top.location.href = document.location.href ;
}
</script>
{/literal}
