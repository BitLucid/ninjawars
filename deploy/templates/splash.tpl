<<<<<<< HEAD
=======
{literal}
<script type='text/javascript' charset='utf-8' src='http://s3.amazonaws.com/getsatisfaction.com/javascripts/feedback-v2.js'></script>

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
 
>>>>>>>   Index: Only shows unread events and unread messages instead of all types.
    
    <!-- Version {$version} -->

    <div id='content' class='wrapper'>
{if $login_error}
      <div id='login-error' class="error">
        That password/username combination was incorrect. <a href='account_issues.php' target='main'>Login/Signup Issues?</a> 
      </div>
{/if}


    <div id="menu" class="login-menu">
      <div id="menu-start">
        <div id="login-bar">
          <form id="login-form" action="index.php#" method="post">
            <span class="text">
              <input type="hidden" name="ref" value="{$referrer|escape}">
                <label>
                  <!-- Username -->
                  <input name="user" type="text" class="itext">
                </label>
                <label>
                  <!-- Password -->
                  <input name="pass" type="password" class="itext">
                </label>
                <input id='login-button' name="action" type="submit" value="Login" class="ibutton formButton">
                <!-- The value of this has to remain lowercase "login" to work with the login system -->
              </span>
            </form>
          </div>


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

      <div id='left-column'>
            
        <div id='ninjawars-home' class='header-section'>
        </div>
        
        <div id='feedback-link'>
                <a style='font-size:2em' class='font-pisan' href="http://ninjawars.proboards.com/index.cgi?action=display&board=suggcomp&thread=1174" target="_blank" class="extLink">Give Feedback</a>
        </div>
          
          <div style='height:110px;position:absolute;bottom:0;'>
            
            <div id='staff-chat'>
            <iframe src="http://www.google.com/talk/service/badge/Show?tk=z01q6amlq2pc0od9ms6m36jk9p0lncem83qghsqacgi5e1akm10254oogtclbbh96d0c5870tgkb63h2b66ntkg7on0crqn451ui988hbpk53ikv71fhsa3d5f84j2shna8ijsf474r80ut32oql3dio4ov6brdi3cn49bpuvi2j40u91hrrrea806bsnlhnelc&amp;w=200&amp;h=60" frameborder="0" allowtransparency="true" width="200" height="60"></iframe>
            </div>
          <div id='contact-us'>
	        <a href='staff.php' target='main'><img src='images/contactstaff.png' alt='Contact Staff'></a>
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
                  <a href='attack_player.php' target='main' title='Travel to different locations in the village.'>                  
                    <img src='images/pagodaIcon_60px.png' alt=''  style='width:60px;height:52px'>Village
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
                  <li><a href="doshin_office.php" target="main">Doshin <img src="images/doshin.png" alt="" style='width:8px;height:8px'></a></li>
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
