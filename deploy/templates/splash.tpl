    {$header}

   
{literal}
<script type='text/javascript' charset='utf-8' src='http://s3.amazonaws.com/getsatisfaction.com/javascripts/feedback-v2.js' type='text/javascript'></script>

<script type="text/javascript" charset="utf-8">
  var feedback_widget_options = {};

  feedback_widget_options.display = "overlay";  
  feedback_widget_options.company = "ninjawars";
  feedback_widget_options.placement = "left";
  feedback_widget_options.color = "#222";
  feedback_widget_options.style = "idea";
  
  var feedback_widget = new GSFN.feedback_widget(feedback_widget_options);
</script>
{/literal}
 
    
    <!-- Version {$version} -->

    <div id='content' class='wrapper'>

      <div id='login-error' class="error {if !$login_error}hidden{/if}">
        That password/username combination was incorrect.  <a href='account_issues.php' target='main'>Login/Signup Issues?</a> 
      </div>


    <div id="menu" class="login-menu">
      <div id="menu-start">
        <div id="login-bar">
          <form id="login-form" action="index.php#" method="post">
            <span class="text">
              <input type="hidden" name="ref" value="{$referrer}">
                <label>
                  <!-- Username -->
                  <input name="user" type="text" class="itext">
                </label>
                <label>
                  <!-- Password -->
                  <input name="pass" type="password" class="itext">
                </label>
                <button name="action" type="submit" value="login" class="ibutton formButton">Login</button>
                <!-- The value of this has to remain lowercase "login" to work with the login system -->
              </span>
            </form>
          </div>


        </div>
        <div id="menu-info">
          <span class="signup-link">
            <a target="main" href="signup.php?referrer={$referrer}">Become a Ninja!</a> |
          </span>
          <span>
            <a href="account_issues.php" target="main" class="blend side">Signup Problems?</a>
          </span>
        </div>
        
      </div>

      <div id='left-column'>
            
        <div id='ninjawars-home' class='header-section'>
        	<a href='list_all_players.php' target='main'><img src='images/ninjawarslogo_75px.png' alt='Ninjawars Intro' title='ninja list'></a>
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
            
          <div id='contact-us'>
	        <a href='staff.php' target='main'><img src='images/contactstaff.png' alt='Contact Staff'></a>
	      </div>

      </div>  
      
      
      
      <!-- CENTRAL COLUMN STARTS HERE -->
      
      
      
      <div id='center-column'>

      
      <div id='menu-bar' class='header-section'>
        <div id='reactive-panel'>

            <div id='category-bar'>
              <ul>
                <li id='combat-actions'>
                  <a href='enemies.php' target='main'>
                    <img src='images/50pxShuriken.png' alt=''>Combat
                  </a>
                </li>
                <li>
				  <div id='ninjawars-title'><a href='tutorial.php' target='main'><img src='images/ninjawars_title.png' alt='Ninja Wars'></a></div>
                </li>
                <li id='village-actions'>
                  <a href='attack_player.php' target='main'>                  
                    <img src='images/pagodaIcon_60px.png' alt=''>Village
                  </a>
                </li>
              </ul>
            </div>
            <div id='subcategory-bar'>
                <ul id='combat-subcategory'>
                  <li><a href="list_all_players.php" target="main">Ninjas</a></li>
                  <li><a href="clan.php" target="main">Clans</a></li>
                  <li><a href="duel.php" target="main">Duels</a></li>
                </ul>
                <ul id='village-subcategory'>
                  <li><a href="shop.php" target="main">Shop</a></li>
                  <li><a href="work.php" target="main">Work</a></li>
                  <li><a href="doshin_office.php" target="main">Doshin <img src="images/doshin.png" alt=""></a></li>
                </ul>
            </div>
        </div>
        
      </div><!-- End of menu-bar -->


          <div id="main-frame-container"><!-- THE MAIN CONTENT DISPLAY SECTION -->
            <iframe border='0' id="main" name="main" class="main-iframe" src="{$main_src}">
              <a href='{$main_src}' target='_blank'>Main Content</a> Display Section (Frames Not Supported)
            </iframe>
          </div><!-- End of mainFrame div -->
          
      </div> <!-- End of center-column -->




      <!-- RIGHTMOST COLUMN STARTS HERE -->


      <div id='right-column'>
      
      
        <div id='ninja-stats' class='header-section'>
        
        
        </div><!-- End of ninja-stats -->

          <div id='ninja-count-menu' class='boxes passive'>
            <!-- <a href="list_all_players.php" target="main">
              <span id='nin1'>Ni</span><span id='nin2'>nj</span><span id='nin3'>as</span> 
              <img src="images/smallArrows.png" alt="&gt;&gt;&gt;">
            </a> -->
            {$players_online} ninjas around / {$player_count} 
          </div>
        
          <div id='index-chat'>
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
                    <iframe border='0' id="mini_chat" name="mini_chat" src="mini_chat.php">
                      <a href='mini_chat.php' target='_blank'>Mini Chat</a> Iframe Display Section (Iframes not supported by this browser)
                    </iframe>
                  </div>
                  <div id="expand-chat">
                    <a href="mini_chat.php?chatlength=360" target="mini_chat">
                      View more chat messages <img class="show-hide-icon" src="images/show_and_hide.png" alt="+/-">
                    </a>
                  </div>
                </div>
              </div>
          </div> <!-- End of index-chat --> 

          <div id="music" class="boxes passive">
            <object type="audio/x-midi" data="files/music/samsho.mid" id="music-player">
              <param name="src" value="files/music/samsho.mid">
              <param name="autoplay" value="true">
              <param name="autoStart" value="0">
              <a href="files/music/samsho.mid">
                Music <img class="play-button" src="images/bullet_triangle_green.png" alt="&gt;">
              </a>
            </object>
          </div>          
          
      </div> <!-- End of right column -->
      <div id='push'></div>
      <div id='index-footer'>
<!-- TODO: make this absolute, floating at the page bottom as per facebook's bar. -->
        <!-- Substitute dynamic "catchphrases" here eventually -->
        <!-- "There was going to be a NinjaWars2, but NinjaWars1 stabbed it." -->
        <!--"Join a clan, promote multiple stab wounds." -->
        Annoy the Emperor, kill Samurai.
         |
        <a href="tutorial.php" target="main">Help</a> |
        <a href="rules.php" target="main">Rules</a> |
        <a href='staff.php' target='main'>Staff</a> |
        <a href="http://ninjawars.proboards.com/index.cgi?board=ann" target="_blank" class="extLink">News</a> |
        <a href="http://ninjawars.proboards.com/index.cgi?action=calendar" target="_blank" class="extLink">Calendar</a> |
        <a href="http://ninjawars.pbwiki.com/" target="_blank" class="extLink">Wiki</a> |
        <a href="http://ninjawars.proboards.com" target="_blank" class="extLink">Forum</a> 
             
      </div>
      
    </div> <!-- End of content div -->
       
<!-- Validated as of Oct, 2009 -->

    <!-- Version: {$version} -->

<!-- body and html tags wrapped around this template via index.php -->
