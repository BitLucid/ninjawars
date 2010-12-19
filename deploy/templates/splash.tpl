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

<style type='text/css'>
{literal}
#top-bar{
	display:inline-block;
	position:relative;
	top:0;
}
#logo-appended{
	position:absolute;
	top:0;
	left:0;
}
#top-logo{
	display:inline-block;
	vertical-align:top;
	margin-left: 2em;
	margin-right: 2em;
}
#contact-us img {
	max-width:98%;
}
#header{
	height:20%;
}
{/literal}
</style>

    <div id='content' class='wrapper'>
    <!-- Top horizontal bar -->
    <div class='header'>
		<div id="logo-appended">
		  <a href="/">
	        <img id='ninjawars-title-image' src='images/halfShuriken.png' alt='Ninja Wars' width='108' height='108'>
		  </a>
		</div>
		<div id='logo-placeholder' style='width:100px;height:100px;display:inline-block;vertical-align:top;z-index:-1'>
		  <!-- Spacer div for the main shuriken linkback logo -->
		</div>
		<div id='top-bar'>
		  <span id='solo-page-login-link'><a href='login.php' class='link-as-button'>Log in</a></span> | <span><a href='signup.php' class='link-as-button'>Signup</a></span>
		</div>
		<div id='top-logo'>
          <a href='main.php' target='main'><img src='images/nw_bamboo_logo_soft.png' alt='' width='200' height='100'></a>
		</div>
      
        <div id='subcategory-bar' style='padding-top:.5em'>
          <ul id='ninjas-subcategory'>
            <li><a href="list.php" target="main">Ninja</a></li>
            <li><a href="clan.php" target="main">Clans</a></li>
            <li><a href="duel.php" target="main">Rumors</a></li>
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
        </div>
      
	  </div><!-- End of header -->
      
      
      <div id='left-column'>
        
		<div id='contact-us' style='margin-top:.5em;margin-bottom:.5em;'>
		  <a href='staff.php' target='main' class='font-shangrila'>Contact Staff</a>
		</div>
		

		<div id='feedback-link'>
		  <a style='font-size:2em' class='font-shangrila extLink' href="http://ninjawars.proboards.com/index.cgi?action=display&board=suggcomp&thread=1174" target="_blank">Give Feedback</a>
		</div>
        
      <div id='chat-housing' style='height:250px;'>
        
{include file="mini-chat.section.tpl"}

	  </div><!-- End of chat-housing -->

      </div><!-- End of left-column -->

      <!-- CENTRAL COLUMN STARTS HERE -->

      <div id='center-column'>
        <div id="main-frame-container"><!-- THE MAIN CONTENT DISPLAY SECTION -->
          <iframe frameBorder='0' id="main" name="main" class="main-iframe" src="{$main_src}">
            <!-- Note the the frameBorder attribute is apparently case sensitive in some versions of ie -->
            <a href='{$main_src}' target='_blank'>Main Content</a> Display Section (Frames Not Supported)
          </iframe>
        </div><!-- End of mainFrame div -->
      </div> <!-- End of center-column -->




      <div id='push'></div>
      <div id='index-footer'>
        <span id='nw-catchphrases'>
{literal}
          <script type="text/javascript">
            $().ready(function (){
                var catchphrases = $('#nw-catchphrases span');
                var rand = Math.floor(Math.random()*catchphrases.size());
                // Choose random index.
                catchphrases.hide().eq(rand).show();
                // Hide all, show one at random.
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
      
      
    </div> <!-- End of content div -->

<!-- Validated as of Oct, 2009 -->

<!-- Version: {$version} -->
