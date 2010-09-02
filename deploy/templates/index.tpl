{literal}
      <script type="text/javascript">
	if (parent.frames.length != 0) {
		location.href = "attack_player.php";
	}
      </script>
      
    <style>
    .unread-count, .unread-count a{
        display:inline-block;
        font-size:10px;
        margin-left:2px;
        padding:1px 5px;
        background:#ddd;
        color:#999;
        font-weight:bold;
        text-shadow:none;
        text-decoration:none;
        border-radius:5px;
        -webkit-border-radius:5px;
        -moz-border-radius:5px;
        color:black;
    }
    .unread-count.message-unread, .unread-count.message-unread a{
        background-color:#4183c4;
        color:#fff;
    }
    </style>
      
{/literal}
    <!-- Version {$version|escape} -->
      <div id='left-column'>
            
        <div id='ninjawars-home' class='header-section'>
        	<a href='list_all_players.php' target='main'><img src='images/ninjawarslogo_75px.png' alt='ninja list' title='Go to the ninja list'></a>
        </div>
            <div>
                <a target="main" href="player.php?player_id={$user_id|escape:'url'|escape}" title='Display your ninja information'>{$username|escape}</a>
            </div>
            <div id='logged-in-bar'>
                <div>
                  <span id='health-status'> </span>
                </div>
            </div>
          <div id="quick-stats" class="boxes">
            <div class="box-title centered">
              <a id='show-hide-quickstats' class="show-hide-link"><!-- jQuery show/hide -->
                Quick Stats
              </a>
            </div>
            <div id="quickstats-and-switch-stats"><!-- Id used by show hide jquery -->
              <div class="centered quickstats-container">
                <a href="quickstats.php" target="quickstats" onclick="return !NW.refreshQuickstats('player');">Stats</a> 
                | <a href="quickstats.php?command=viewinv" target="quickstats" onclick="return !NW.refreshQuickstats('viewinv');">Inventory</a>
              </div>
              <div id="quickstats-frame-container"><div></div>
                <noscript>
                <iframe frameBorder='0' id="quickstats" src="quickstats.php" name="quickstats">
                  <!-- Note the the frameBorder attribute is apparently case sensitive in some versions of ie -->
                  <a href='quickstats.php' target='_blank'>Quick Stats</a> unavailable inside this browser window.
                </iframe>
                </noscript>
              </div>
            </div><!-- End of quickstats and switch container -->
          </div><!-- End of quickstats section. -->
          
          <div id="actions" class="boxes active">
            <div class="box-title">
              <a id='show-hide-actions-menu' class="show-hide-link"><!-- jQuery show/hide -->
                Actions
              </a>
            </div>
            <ul class="basemenu" id="actions-menu"><!-- Id used by show hide jquery -->
              <li id='heal-link'><a href="shrine_mod.php?heal_and_resurrect=1" target="main"><img src='images/shrine.png' alt=''>Heal</a></li>
              <li>
                <ul class="submenu">
                  <li>
                    <a href="inventory_mod.php?item=Stealth%20Scroll&amp;selfTarget=1&amp;link_back=inventory"
                     target="main">Stealth</a>
                  </li>
                  <li>
                    <a href="inventory_mod.php?item=Speed%20Scroll&amp;selfTarget=1&amp;link_back=inventory" 
                    target="main">Speed</a>
                  </li>
                </ul>
              </li>
            </ul>
          </div>


        <div id="ninja-search" class="boxes active">
            <form id="player_search" action="list_all_players.php" target="main" method="get" name="player_search">
              <div>
                Find A Ninja:
                <input id="searched" type="text" maxlength="50" size="10" name="searched" class="textField">
                <input id="hide" type="hidden" name="hide" value="dead">
                <button type="submit" value="find" class="formButton">Find</button>
              </div>
            </form>
          </div>

{if $player_info.level < 2}
          <div id='helpful-info'>
            <a target='main' href='tutorial.php'>Helpful Info</a>
          </div>
{/if}



          <!-- Recent Events count and target will get put in here via javascript -->
          <div id='recent-events' class="boxes active" style='display:none'>
            <!--<div>
                <a id='view-events' target='main' href='events.php' title='View events'>
                  Unread Events <span class='unread-events-count unread-count'>0</span>
                </a>
            </div>-->
              
            <div>
                <a target='main' id='recent-event-attacked-by' href='events.php' title='View events'>
                      You weren't recently in combat
                </a> with 
                <a id='view-event-char' target='main' href='#' title="View a player's profile">
                  anyone
                </a>.
            </div>
          </div>

      </div>  
      
      
      
      <!-- CENTRAL COLUMN STARTS HERE -->
      
      
      
      <div id='center-column'>

      
      <div id='menu-bar' class='header-section'>
        <div id='reactive-panel'>

            <div id='category-bar'>
              <ul>
                <li id='status-actions'>
                  <a href='events.php' target='main' title='See messages about whether you were attacked or other events.'>
                    <img src='/images/ninja_status_icon_50px.png' alt='' style='width:50px;height:51px'>Status
                  </a>
                </li>
                <li id='combat-actions'>
                  <a href='enemies.php' target='main' title='Check up on your enemies and see who recently attacked you.'>
                    <img src='images/50pxShuriken.png' alt=''  style='width:50px;height:42px'>Combat
                  </a>
                </li>
                <li id='village-actions'>
                  <a href='attack_player.php' target='main' title='Travel to different locations in the village.'>                  
                    <img src='images/pagodaIcon_60px.png' alt=''  style='width:60px;height:52px'>Village
                  </a>
                </li>
              </ul>
            </div>
            <div id='subcategory-bar'>
                <ul id='self-subcategory'>
                  <li><a href="stats.php" target="main" title='Your ninja strength, level, profile, etc.'>Stats</a></li>
                  <li><a href="skills.php" target="main" title='Your ninja skills &amp; abilities'>Skills</a></li>
                  <li><a href="inventory.php" target="main" title='Your items and links to use them on yourself.'>Items</a></li>
                  <!-- Profile -->
                  <!-- Settings -->
                </ul>
                <ul id='combat-subcategory'>
                  <li><a href="list_all_players.php" target="main" title='Ranked list of ninjas to attack.'>Ninjas</a></li>
                  <li><a href="clan.php" target="main" title='Clans and your clan options.'>Clans</a></li>
                  <li><a href="duel.php" target="main" title="Today's Duels">Duels</a></li>
                </ul>
                <ul id='village-subcategory'>
                  <li><a href="shop.php" target="main" title='Spend your money to get weapons.'>Shop</a></li>
                  <li><a href="work.php" target="main" title='Trade your turns to get money.'>Work</a></li>
                  <li><a href="doshin_office.php" target="main" title='Hunt bounties for money.'>Doshin <img src="images/doshin.png" alt="" style='height:8px;width:8px'></a></li>
                </ul>
            </div>
        </div>
        
      </div><!-- End of menu-bar -->


          <div id="main-frame-container"><!-- THE MAIN CONTENT DISPLAY SECTION -->
            <iframe frameBorder='0' id="main" name="main" class="main-iframe" src="{$main_src|escape}">
            <!-- Note the the frameBorder attribute is apparently case sensitive in some versions of ie -->
              <a href='{$main_src|escape}' target='_blank'>Main Content</a> unavailable inside this browser window.
            </iframe>
          </div><!-- End of mainFrame div -->
          
      </div> <!-- End of center-column -->




      <!-- RIGHTMOST COLUMN STARTS HERE -->


      <div id='right-column'>
      
      
        <div id='ninja-stats' class='header-section'>
        
        <div id='logout'>
            <a href="index.php?logout=true"><img src='{$templatelite.const.IMAGE_ROOT|escape}logoutTriangle.png' alt='Logout' title='Log off the game' style='height:70px;width:70px'></a>
        </div>
        
        </div><!-- End of ninja-stats div -->
          
          
          <div id='messages' class='boxes active'>
              <div>
                  <a target="main" id='message-inbox' href="messages.php">Messages<img id='messages-icon' src='images/messages.png' alt=''>
                    <span class='unread-count'>{$unread_message_count}</span>
                  </a>
              </div>
          </div>
        
{include file="mini-chat.section.tpl"}

          <div id="music" class="boxes passive">
            <object type="audio/x-midi" data="files/music/samsho.mid" id="music-player" style='width:100%'>
              <param name="src" value="files/music/samsho.mid">
              <param name="controller" value="0">
              <param name="autoplay" value="0">
              <param name="autoStart" value="0">
              <embed src="files/music/samsho.mid" type='audio/midi' controller='true' hidden="false" style='width:100%;border:0;' autostart="0" autoplay="0" loop="true" volume="60%">
              <a href="files/music/samsho.mid">
                Music <img class="play-button" src="images/bullet_triangle_green.png" alt="&gt;">
              </a>
            </object>
          </div>          
          
      </div> <!-- End of right column -->
      <div id='push'></div>
      <div id='index-footer'>
        <span id='nw-catchphrases'>
        {literal}
        <script>
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
        <span style="display:none">Helping ninja stab people since 2003.</span>
        <span style="display:none">Fact: Ninja can just click faster.</span>
        <span>Oni are actually quite friendly, if you get to know them.</span>
        </span>
        |
        <a href="tutorial.php" target="main">Help</a> |
        <a href="rules.php" target="main">Rules</a> |
        <a href='staff.php' target='main'>Staff</a> |
        <a href='http://google.com/search?q=site%3Awww.ninjawars.net' target='_blank' class='extLink'>Search</a> |
        <a href="http://ninjawars.proboards.com/index.cgi?board=ann" target="_blank" class="extLink">News</a> |
        <a href="http://ninjawars.pbworks.com/" target="_blank" class="extLink">Wiki</a> |
        <a href="http://forum.ninjawars.net" target="_blank" class="extLink">Forum</a> |
        <a href="http://ninjawars.proboards.com/index.cgi?action=display&board=suggcomp&thread=1174" target="_blank" class="extLink">Feedback</a>
      </div>
      
    
<!-- Validated as of Oct, 2009 -->

<!-- Version: {$version|escape} -->
