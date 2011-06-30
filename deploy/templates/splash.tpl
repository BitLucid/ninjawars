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
      	<div id='footer-top-bar'>
        <span id='nw-catchphrases'>
{literal}
          <script type="text/javascript">
            $().ready(function (){
                var catchphrases = $('#nw-catchphrases span');
                var rand = Math.floor(Math.random()*catchphrases.size());
                // Choose random index.
                catchphrases.hide().eq(rand).show();
                // Hide all, show one at random.
                
                var footer = $('#index-footer');
                //Hide the second two sections.
                var footerBottoms = footer.find('#footer-middle-bar, #footer-bottom-bar').hide();
                // When any of the three sections are hovered, show the bottom two.
        // Only change the display of the bottom sections if another event doesn't over-ride.
                footer.hover(
                	function(){footerBottoms.stop(true, true).slideDown()}, 
                	function(){footerBottoms.stop(true, true).delay(2000).slideUp()}
                );
                
            });
          </script>
{/literal}
        <!-- These catchphrases will be displayed randomly. -->
          <span style="display:none">There was going to be a NinjaWars2, but NinjaWars1 stabbed it.</span>
          <span style="display:none">Join a clan, promote multiple stab wounds.</span>
          <span style="display:none">Annoy the Emperor, kill Samurai.</span>
          <span style="display:none">Some theorize that poison is actually liquified ninja.</span>
          <span style="display:none">Helping ninja stab people since 2003.</span>
          <span style="display:none">Fact: Ninja can just click faster.</span>
          <span>Oni are actually quite friendly, if you get to know them.</span>
        </span>
         |
        <a href="tutorial.php" target="main">Help</a> |
        <a href="rules.php" target="main">Rules</a> |
        <a href="http://ninjawars.proboards.com/index.cgi?board=ann" target="_blank" class="extLink">News</a> |
        <a href="http://ninjawars.pbwiki.com/" target="_blank" class="extLink">Wiki</a> |
        <a href="http://ninjawars.proboards.com" target="_blank" class="extLink">Forum</a> |
        <a href="http://ninjawars.proboards.com/index.cgi?action=display&board=suggcomp&thread=1174" target="_blank" class="extLink">Feedback</a>
        </div>
        <div id='footer-middle-bar'>
		    <span id='created-by'>
		    	<a href='staff.php' target='main'>CREATED BY</a>
		    </span>
		    <div id='footer-authors'>
		    	<span class='author'>
					<a href='//royronalds.com' class='extLink'>Roy Ronalds</a>
					<a href='player.php?target=tchalvak'>Ninja: Tchalvak</a>
					<a href='//twitter.com/tchalvak' class='extLink'>@tchalvak</a>
		    	</span>
		    	<span class='author'>
		    		<a>Al Vazquez</a>
		    		<a href='player.php?target=beagle'>Ninja: Beagle</a>
		    	</span>
		    </div>
        </div>
        <div id='footer-bottom-bar'>
        	<span>
		    <a href="http://www.w3.org/html/logo/">
			<img src="http://www.w3.org/html/logo/badge/html5-badge-h-css3-multimedia-performance-semantics.png" width="229" height="64" alt="HTML5 Powered with CSS3 / Styling, Multimedia, Performance &amp; Integration, and Semantics" title="HTML5 Powered with CSS3 / Styling, Multimedia, Performance &amp; Integration, and Semantics">
			</a>
			</span>
        	<script type='text/javascript' src="js/staffPage.js"></script>
        	<script>
        	{literal}
			$(document).ready(function() {
				loadLastCommitMessage();
			});
        	{/literal}
        	</script>
			<div id='latest-commit-section'>
				<p id='latest-commit-title' style='display:none'>Most recent upcoming change to ninjawars:</p>
				<span id='latest-commit' style='display:none'>
				</span>
			</div>
        </div>
      </footer>
      
      
    </div> <!-- End of content div -->

<!-- Validated as of Oct, 2009 -->

<!-- Version: {$version} -->
