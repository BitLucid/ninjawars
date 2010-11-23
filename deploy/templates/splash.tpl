    <!-- Version {$version} -->
{literal}
      <script type="text/javascript">
	if (parent.frames.length != 0) {
{/literal}
		location.href = "{$main_src}";
{literal}
	}
      </script>
{/literal}

    <div id='content' class='wrapper'>
{if $login_error}
      <div id='login-error' class="error">
        {$login_error|escape} <a href='account_issues.php' target='main'>Login/Signup Issues?</a>
      </div>
{/if}

      <div id="menu" class="login-menu">
        <div id="menu-start">

{include file='login-bar.tpl' referrer=$referrer stored_username=$stored_username}

        </div>
        <div id="menu-info">
          <span class="signup-link">
            <a target="main" href="signup.php?referrer={$referrer|escape}">Become a Ninja!</a> |
          </span>
          <span>
            <a href="account_issues.php" target="main" class="blend side">Signup Problems?</a>
          </span>
        </div>
      </div>
      <div id='left-column' style='position:relative;height:100%;min-height:600px'>
        <div id='ninjawars-home' class='header-section'>
        </div>
        <div id='feedback-link'>
          <a style='font-size:2em' class='font-pisan' href="http://ninjawars.proboards.com/index.cgi?action=display&board=suggcomp&thread=1174" target="_blank" class="extLink">Give Feedback</a>
        </div>
        <div style='height:110px;position:absolute;bottom:112px;'>
          <div id='contact-us'>
            <a href='staff.php' target='main' class='font-pisan'>Contact Staff</a>
          </div>
        </div>
      </div>

      <!-- CENTRAL COLUMN STARTS HERE -->

      <div id='center-column'>
        <div id='menu-bar' class='header-section'>
          <div id='reactive-panel'>
            <div id='category-bar'>
              <ul>
                <li id='combat-actions'>
                  <a href='enemies.php' target='main' title='Check up on your enemies and see who recently attacked you.'>
                    <img src='images/50pxShuriken.png' alt=''  style='width:50px;height:42px'>Combat
                  </a>
                </li>
                <li>
				  <div id='ninjawars-title'><a href='tutorial.php' target='main'><img id='ninjawars-title-image' src='images/ninjawars_title.png' alt='Ninja Wars' style='width:428px;height:100px'></a></div>
                </li>
                <li id='village-actions'>
                  <a href='map.php' target='main' title='Travel to different locations on the map.'>
                    <img src='images/pagodaIcon_60px.png' alt=''  style='width:60px;height:52px'>Map
                  </a>
                </li>
              </ul>
            </div>
            <div id='subcategory-bar'>
              <ul id='combat-subcategory'>
                <li><a href="list.php" target="main">Ninja</a></li>
                <li><a href="clan.php" target="main">Clans</a></li>
                <li><a href="duel.php" target="main">Rumors</a></li>
              </ul>
              <ul id='village-subcategory'>
                <li><a href="shop.php" target="main">Shop</a></li>
                <li><a href="work.php" target="main">Work</a></li>
                <li>
                  <a href="doshin_office.php" target="main">Doshin <img src="images/doshin.png" alt="" style='width:8px;height:8px'></a>
                </li>
              </ul>
            </div>
          </div>
        </div><!-- End of menu-bar -->
        <div id="main-frame-container"><!-- THE MAIN CONTENT DISPLAY SECTION -->
          <iframe frameBorder='0' id="main" name="main" class="main-iframe" src="{$main_src}">
            <!-- Note the the frameBorder attribute is apparently case sensitive in some versions of ie -->
            <a href='{$main_src}' target='_blank'>Main Content</a> Display Section (Frames Not Supported)
          </iframe>
        </div><!-- End of mainFrame div -->
      </div> <!-- End of center-column -->

      <!-- RIGHTMOST COLUMN STARTS HERE -->

      <div id='right-column'>
        <div id='ninja-stats' class='header-section'>
        </div><!-- End of ninja-stats -->

{include file="mini-chat.section.tpl"}

		{* Took out the music box on splash
        <div id="music" class="boxes passive">
{include file='music.tpl'}
		*}
        </div>
      </div> <!-- End of right column -->
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
