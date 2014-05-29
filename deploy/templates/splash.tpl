<style>
{literal}
.splash #top-logo{
  width:50%;text-align:center;display:inline-block;vertical-align:top;margin-left: 2em;margin-right: 2em;
}
.splash #menu-map-head{
  font-size:xx-large;margin-right:.5em;
}
.splash .doshin-image{
  width:8px;height:8px;
}
{/literal}
</style>
    <!-- Version {$version} -->

    <!-- Top horizontal bar -->
    <header id='index-header' class='clearfix'>
		<div id="logo-appended">
		  <a href="/">
	        <img id='ninjawars-title-image' src='images/halfShuriken.png' title='Home' alt='Ninja Wars' width='108' height='108'>
		  </a>
		</div>
		<div id='logo-placeholder'>
		  <!-- Spacer div for the main shuriken linkback logo -->
		</div>
		<nav role='navigation' id='top-bar' class='navigation'>
		  <span id='solo-page-login-link'><a href='login.php' class='link-as-button'>Log in</a></span> | <span><a href='signup.php' class='link-as-button'>Signup</a></span>
		</nav>
		<nav id='top-logo'>
          <a href='main.php' target='main'><img src='images/nw_bamboo_logo_soft.png' alt='NinjaWars' width='200' height='100'></a>
		</nav>
      
        <nav role='navigation' id='subcategory-bar' class='navigation'>
          <ul id='ninjas-subcategory'>
            <li><a href="list.php" target="main">Ninjas</a></li>
            <li><a href="clan.php" target="main">Clans</a></li>
          </ul>
          <ul id='map-subcategory'>
          	<li><a href='map.php' id='menu-map-head' target='main' title='Travel to different locations on the map'>Map&rarr;</a></li>
            <li><a href="shop.php" target="main">Shop</a></li>
            <li><a href="work.php" target="main">Field</a></li>
            <li>
              <a href="doshin_office.php" target="main">Doshin <img class='doshin-image' src="images/doshin.png" alt=""></a>
            </li>
          </ul>
        </nav> <!-- End of subcategory bar -->
      
	  </header><!-- End of header -->
      

      <div id='core' class='clearfix'>
      <!-- MAIN COLUMN STARTS HERE -->
		{include file="core.tpl"}    
      
      <aside id='sidebar-column'  class='navigation'>
		<div id='contact-us' class='thick'>
		  <a href='staff.php' target='main' class='font-shangrila'>Contact Staff</a>
		</div>
		

		<div id='feedback-link'>
		  <a style='font-size:2em' class='font-shangrila extLink' href="http://ninjawars.proboards.com/index.cgi?action=display&amp;board=suggcomp&amp;thread=1174" target="_blank">Give Feedback</a>
		</div>

  {if isset($show_news) and $show_news}
      <div id='news-housing' style='height:80px;'>
        
{include file="mini-news.section.tpl"}

    </div><!-- End of news-housing -->
  {/if}
        
      <div id='chat-housing' style='height:250px;'>
        
{include file="mini-chat.section.tpl"}

	  </div><!-- End of chat-housing -->


      </aside><!-- End of aside -->
      
      </div><!-- End of core -->

      <!-- <div id='push'></div> -->
      <footer id='index-footer'  class='navigation'>
      
            <!-- Stuff like catchphrases, links, and the author information -->
      {include file='linkbar_section.tpl'}

        
      </footer>
      
      
<!-- Validated as of Oct, 2009 -->

<!-- Version: {$version} -->

{literal}
<script>
if (top.location != location) { // Framebreak on the splash page to prevent any issues.
  top.location.href = document.location.href ;
}
</script>
{/literal}