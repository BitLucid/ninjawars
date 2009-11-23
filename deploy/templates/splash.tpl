    {$header}
    
    <!-- Version {$version} -->

  <div id="content">

    <div id="menu" class="login-menu">
      <div id="menu-start">
        <div id="login-bar">
          <form id="login-form" action="{$WEB_ROOT}index.php#" method="post">
            <span class="text">
              <input type="hidden" name="ref" value="{$referrer}">
                <label>
                  Username:
                  <input name="user" type="text" class="itext">
                </label>
                &nbsp;
                <label>
                  Password:
                  <input name="pass" type="password" class="itext">
                </label>
                <input name="action" type="submit" value="login" class="ibutton formButton">
              </span>
            </form>
          </div>


        </div>
        <div id="menu-info">
          <span class="signup-link">
            <a target="main" href="{$WEB_ROOT}signup.php?referrer={$referrer}">Become a Ninja!</a> |
          </span>
          <span>
            <a href="{$WEB_ROOT}lostpass.php" target="main" class="blend side">&nbsp;Lost&nbsp;Password?</a> |
          </span>
                  
          <a href="rules.php" target="main">Rules</a> |
          <a href="tutorial.php" target="main">Intro</a> |
          <a href="http://ninjawars.pbwiki.com/" target="_blank" class="extLink">Wiki 
            <img class="extLink" src="{$IMAGE_ROOT}externalLinkGraphic.gif" alt=""></a> 
          | <a href="http://ninjawars.proboards19.com" target="_blank" class="extLink">Forum </a>
            <img class="extLink"  src="{$IMAGE_ROOT}externalLinkGraphic.gif" alt="">
          | <a href="http://ninjawars.proboards19.com/index.cgi?board=ann" target="_blank" class="extLink">News</a>
             <img class="extLink" src="{$IMAGE_ROOT}externalLinkGraphic.gif" alt="">
        </div>
        
        <div id="menu-end">
        </div>
      </div>

{if $login_error}
      <div id='login-error' class="error">
        That password/username combination was incorrect.  
        If needed, you can request help
        <a target='_blank' href='http://ninjawars.proboards.com/index.cgi?board=bug&amp;action=display&amp;thread=1051'>
        login problems</a> on the forum.
      </div>
{/if}

      <div class="three-columns">

        <!-- LEFT COLUMN -->
        <div id="leftColumn" class="column">

          <div id='ninja-count-menu' class='boxes passive'>
            <div class='box-title'>
              <a href='#' class='show-hide-link ninja-count' onclick="toggle_visibility('ninja-count');">
                Ninjas: <img class='show-hide-icon' src='{$IMAGE_ROOT}show_and_hide.png' alt='+/-'>
              </a>
            </div>
            <div id='ninja-count'><p>{$players_online} Online </p><p> {$player_count} Total</p></div>
          </div>

          <div id="music" class="boxes passive">
            <object type="audio/x-midi" data="{$WEB_ROOT}music/samsho.mid" id="music-player">
              <param name="src" value="{$WEB_ROOT}music/samsho.mid">
              <param name="autoplay" value="true">
              <param name="autoStart" value="0">
              <a href="{$WEB_ROOT}music/samsho.mid">
                Music <img class="play-button" src="{$IMAGE_ROOT}bullet_triangle_green.png" alt="&gt;">
              </a>
            </object>
          </div>

          <div id="links" class="boxes passive">
            <div class="box-title">
              <a href="#" class="show-hide-link links-menu" onclick="toggle_visibility('links-menu');">
                Links <img class="show-hide-icon" src="{$IMAGE_ROOT}show_and_hide.png" alt="+/-">
              </a>
            </div>
            <ul id="links-menu">
              <li><a href="staff.php" target="main">Staff</a></li>
              <li><a href="duel.php" target="main">Duels</a></li>
              <!--  <a href="vote.php" target="main">Vote For NW </a>  -->
              <li>
                <a href="http://ninjawars.proboards19.com/index.cgi?action=calendar" target="_blank" class="extLink">
                  Calendar <img class="extLink" src="{$IMAGE_ROOT}externalLinkGraphic.gif" alt=""></a>
              </li>
            </ul>
          </div>
        </div><!-- End of left Column div-->

<!-- Substitute image and "catchphrases" here eventually -->

        <div id="centerColumn" class="column"><!-- top menu starts here -->
          <div id="main-frame-container"><!-- THE MAIN CONTENT DISPLAY SECTION -->
            <iframe id="main" name="main" class="main-iframe" src="{$main_src}">
              Main Content Display Section (Frames Not Supported)
            </iframe>
          </div><!-- End of mainFrame div -->

<!-- LOCATION FOR CONTENT UNDER THE MAIN DISPLAY SECTION -->
<!--
          <div class="created-by">
            catchphrase games
          </div>
-->
        </div><!-- End of centerColumn div -->

        <div id="rightColumn" class="column"><!-- RIGHT COLUMN -->
          <div id="player-list" class="boxes special centered">
            <a href="list_all_players.php" target="main">
              <span id='nin1'>Ni</span><span id='nin2'>nj</span><span id='nin3'>as</span> 
              <img src="images/smallArrows.png" alt="&gt;&gt;&gt;">
            </a>
          </div>
          <div id="ninja-search" class="boxes active">
            <div class="box-title centered">Ninja Search</div>
            <form id="player_search" action="list_all_players.php" target="main" method="get" name="player_search">
              <div>
                Ninja:
                <input id="searched" type="text" maxlength="50" name="searched" class="textField">
                <input id="hide" type="hidden" name="hide" value="dead">
                <input type="submit" value="find" class="formButton">
              </div>
            </form>
          </div>
{if $is_logged_in}
          <div id="quick-stats" class="boxes">
            <div class="box-title centered">
              <a href="#" class="show-hide-link" onclick="toggle_visibility('quickstats-and-switch-stats');">
                Quick Stats <img class="show-hide-icon" src="images/show_and_hide.png" alt="+/-">
              </a>
            </div>
            <div id="quickstats-and-switch-stats">
              <div class="centered quickstats-container">
                <a href="quickstats.php" target="quickstats">Player</a> 
                | <a href="quickstats.php?command=viewinv" target="quickstats">Inventory</a>
              </div>
              <div id="quickstats-frame-container">
                <iframe id="quickstats" src="quickstats.php" name="quickstats">
                  Quick Stats Iframe Display section (Iframes Not supported by this browser)
                </iframe>
              </div>
            </div><!-- End of quickstats and switch container -->
          </div><!-- End of quickstats section. -->
<!-- End of display when logged in -->
{/if}
          <div id="village-chat" class="boxes active">
            <div class="box-title centered">
              <a href="#" class="show-hide-link" onclick="toggle_visibility('chat-and-switch');">
                Chat <img class="show-hide-icon" src="images/show_and_hide.png" alt="+/-">
              </a>
            </div>
            <div id="chat-and-switch">
              <div class="chat-switch centered">
                <a href="village.php" target="main">Full Chat <img src="images/chat.png" alt=""> </a>
                <a href="mini_chat.php?chat_length=20" target="mini_chat">Refresh</a>
              </div>
<!-- TODO: move chat submit box out here. -->
              <div id="mini-chat-frame-container" class='chat-collapsed'>
                <iframe id="mini_chat" name="mini_chat" src="mini_chat.php">
                  Mini Chat Iframe Display Section (Iframes not supported by this browser)
                </iframe>
              </div>
              <div id="expand-chat">
                <a href="mini_chat.php?chatlength=360" target="mini_chat">
                  View more messages <img class="show-hide-icon" src="images/show_and_hide.png" alt="+/-">
                </a>
              </div>
            </div>
          </div>
        </div><!--- End of rightColumn div -->

      </div><!-- End of columns div -->

    </div><!-- End of bodyContent div -->

<!-- Validated as of Feb, 2009 with notices about self-closing br tags. -->

    <!-- Version: {$version} -->

  </body>
</html>
