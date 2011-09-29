    <!-- Version {$version} -->
{literal}
      <script type="text/javascript">
      // Break out of any outer frames.
	if (parent.frames.length != 0) {
{/literal}
		location.href = "{$main_src}";
{literal}
	}
      </script>
{/literal}
    <div id='content' class='wrapper'>
    <!-- Top horizontal bar -->
    <header class='header'>
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
		<nav id='top-logo' style='width:50%;text-align:center;display:inline-block;vertical-align:top;margin-left: 2em;margin-right: 2em;'>
          <a href='main.php' target='main'><img src='images/nw_bamboo_logo_soft.png' alt='' width='200' height='100'></a>
		</nav>
      
        <nav role='navigation' id='subcategory-bar' class='navigation'>
          <ul id='ninjas-subcategory'>
            <li><a href="list.php" target="main">Ninjas</a></li>
            <li><a href="clan.php" target="main">Clans</a></li>
          </ul>
          <ul>
          	<li>&nbsp;</li>
            <!-- Placeholder to fill space-->
          </ul>
          <ul id='map-subcategory'>
          	<li><a href='map.php' id='menu-map-head' target='main' title='Travel to different locations on the map' style='font-size:xx-large;margin-right:.5em;'>Map&rarr;</a></li>
            <li><a href="shop.php" target="main">Shop</a></li>
            <li><a href="work.php" target="main">Field</a></li>
            <li>
              <a href="doshin_office.php" target="main">Doshin <img src="images/doshin.png" alt="" style='width:8px;height:8px'></a>
            </li>
          </ul>
        </nav> <!-- End of subcategory bar -->
      
	  </header><!-- End of header -->
      

      
      <!-- MAIN COLUMN STARTS HERE -->
      <div id='main-column'>
        <div id="main-frame-container"><!-- THE MAIN CONTENT DISPLAY SECTION -->
          <iframe frameBorder='0' id="main" name="main" class="main-iframe" src="{$main_src}">
            <!-- Note the the frameBorder attribute is apparently case sensitive in some versions of ie -->
            <a href='{$main_src}' target='_blank'>Main Content</a> Display Section (Frames Not Supported)
          </iframe>
        </div><!-- End of mainFrame div -->
      </div> <!-- End of main-column -->      
      
      <aside id='sidebar-column'  class='navigation'>
		<div id='contact-us' style='margin-top:.5em;margin-bottom:.5em;'>
		  <a href='staff.php' target='main' class='font-shangrila'>Contact Staff</a>
		</div>
		

		<div id='feedback-link'>
		  <a style='font-size:2em' class='font-shangrila extLink' href="http://ninjawars.proboards.com/index.cgi?action=display&board=suggcomp&thread=1174" target="_blank">Give Feedback</a>
		</div>
        
      <div id='chat-housing' style='height:250px;'>
        
{include file="mini-chat.section.tpl"}

	  </div><!-- End of chat-housing -->

      </aside><!-- End of right-aside -->

      <!-- <div id='push'></div> -->
      <footer id='index-footer'  class='navigation'>
      
            <!-- Stuff like catchphrases, links, and the author information -->
      {include file='linkbar_section.tpl'}

        
      </footer>
      
      
    </div> <!-- End of content div -->

<!-- Validated as of Oct, 2009 -->

<!-- Version: {$version} -->
