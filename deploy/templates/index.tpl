    <!-- Version {$version|escape} -->
{literal}
      <script type="text/javascript">
      // Index-only javascript


		if (parent.frames.length != 0) { // If there is a double-nested index...
			location.href = "main.php"; // ...Display the main page instead.
			// This function must be outside of domready, for some reason.
		}      
      
      $(document).ready(function(){		
		
		
		$('#donation-button').hide().delay('3000').slideDown('slow').delay('20000').slideUp('slow');
		// Hide, show, and then eventually hide the donation button.
		

		// Hide the self and map subcategories initially, leaving the combat subcategory visible
		var subcats = $('#self-subcategory, #map-subcategory').hide();
		
		//delay('2000').slideUp('slow');
	
		// Find the trigger areas and show the appropriate subcategory.
		var triggers = $('#category-bar').find('.combat, .self, .map');
		if(triggers){
			triggers.mouseenter(function(){
				var trigger = $(this);
				var triggeredSubcat = $("#"+trigger.attr('class')+'-subcategory').show().siblings().hide();
		    	// When a different trigger area is hovered, hide the other subcats.
			});
		}
		
		
	
	});
	</script>
      
{/literal}


<style type='text/css'>
{literal}
#logo-appended{
	position:absolute;
	top:0;
	left:0;
}
#header{
	height:20%;
}
{/literal}
</style>

	<div id="logo-appended">
	  <a href="/">
        <img id='ninjawars-title-image' src='images/halfShuriken.png' title='Home' alt='Ninja Wars' width='100' height='100'>
	  </a>
	</div>

    <header class='header'>

	  <div id='logo-placeholder'>
	    <!-- Spacer div for the main shuriken linkback logo -->
	    &nbsp;
	  </div>
	  <div id='health-and-turns' class='various-bars' style='width:46%;display:inline-block;vertical-align:top;margin:0 15% .1em;'>
	  	<div id='barstats' style='width:100%;display:none;height:100%;font-size:.9em'>
		  	<!-- Display the number bars for various char stats-->
		  	<div id='health' style='height:33%'>
			  {include file="generic_bar.tpl" bar_percent=$player_info.hp_percent number=$player_info.health zero_word='Dead' number_of='Health' bar_color='#660000' title='Heal Yourself' action='shrine_mod.php?heal_and_resurrect=1'}<!-- #ee2520 -->
		  	</div>
		  	<div id='turns' style='height:33%'>
			  {include file="generic_bar.tpl" bar_percent=$player_info.turns_percent number=$player_info.turns zero_word='No Turns' number_of='Turns' bar_color='#003366' title='Speed Up' action='inventory_mod.php?item=amanita&amp;selfTarget=1'}	
		  	</div>
		  	<div id='kills' style='height:33%'>
			  {include file="generic_bar.tpl" bar_percent=$player_info.exp_percent number=$player_info.kills zero_word='No Kills' number_of='Kills' bar_color='#330066' title='View Stats' action='stats.php'}<!-- #6612ee -->
		  	</div>
	  	</div>
	  </div>

		<div id='logout'>
		    <a href="logout.php">
		      <img src='{$smarty.const.IMAGE_ROOT|escape}logoutTriangle.png' alt='Logout' title='Leave the game' style='height:60px;width:60px'>
		    </a>
		</div>

      <div id='menu-bar' class='header-section'>
        <div id='reactive-panel'>
            <nav id='category-bar' class='navigation'>
              <ul>
                <li id='status-actions' class='self'>
                  <a href='events.php' target='main' title='See messages about whether you were attacked or other events.'>
                    <img src='/images/ninja_status_icon_50px.png' alt='' style='width:50px;height:51px'>Watch
                  </a>
                </li>
                <li id='combat-actions' class='combat'>
                  <a href='enemies.php' target='main' title='Check up on your enemies and see who recently attacked you.'>
                    <img src='images/50pxShuriken.png' alt=''  style='width:50px;height:42px'>Fight
                  </a>
                </li>
                <li id='map-actions' class='map'>
                  <a href='map.php' target='main' title='Travel to different locations on the Map.'>                  
                    <img src='images/pagodaIcon_60px.png' alt=''  style='width:60px;height:52px'>Move
                  </a>
                </li>
              </ul>
            </nav>
            <nav id='subcategory-bar' class='navigation'>
                <ul id='self-subcategory'>
                  <li><a href="stats.php" target="main" title='Your ninja strength, level, profile, etc.'>Self</a></li>
                  <li><a href="skills.php" target="main" title='Your ninja skills &amp; abilities'>Skills</a></li>
                  <li><a href="inventory.php" target="main" title='Your items and links to use them on yourself.'>Items</a></li>
                  <!-- Profile -->
                  <!-- Settings -->
                </ul>
                <ul id='combat-subcategory'>
                  <li><a href="list.php" target="main" title='Ranked list of ninjas to attack.'>Ninjas</a></li>
                  <li><a href="clan.php" target="main" title='Clans and your clan options.'>Clans</a></li>
                </ul>
                <ul id='map-subcategory'>
                  <li><a href="shop.php" target="main" title='Spend your money to get weapons.'>Buy</a></li>
                  <li><a href="work.php" target="main" title='Trade your turns to get money.'>Work</a></li>
                  <li><a href="doshin_office.php" target="main" title='Hunt bounties for money.'>Hunt<img src="images/doshin.png" alt="" style='height:8px;width:8px'></a></li>
                </ul>
            </nav>
        </div><!-- End of reactive panel -->
        
      </div><!-- End of menu-bar -->


            
	  </header><!-- End of header -->
      
      
      
      <!-- MAIN COLUMN STARTS HERE -->
      <div id='main-column'>


          <div id="main-frame-container"><!-- THE MAIN CONTENT DISPLAY SECTION -->
            <iframe frameBorder='0' id="main" name="main" class="main-iframe" src="{$main_src|escape}">
            <!-- Note the the frameBorder attribute is apparently case sensitive in some versions of ie -->
              <a href='{$main_src|escape}' target='_blank'>Main Content</a> unavailable inside this browser window.
            </iframe>
          </div><!-- End of mainFrame div -->
          
      </div> <!-- End of main-column -->


      <!-- SIDEBAR COLUMN STARTS HERE -->
      <aside id='sidebar-column'>
            <div>
                <a target="main" href="player.php?player_id={$user_id|escape:'url'|escape}" title='Display your ninja information'>
                	<strong class='char-name'>{$username|escape}</strong>
                </a>
            </div>


{if $new_player}
          <div id='helpful-info'>
            <a target='main' href='tutorial.php'>Helpful Info</a>
          </div>
{/if}





          <!-- Recent Events count and target will get put in here via javascript -->
          
          <div id='messages' class='boxes active'>
              <div>
                  <a target="main" id='message-inbox' href="messages.php">Messages<img id='messages-icon' src='images/messages.png' alt=''>
                    <span class='unread-count'>{$unread_message_count}</span>
                  </a>
                  
	  {if !$new_player}
	  <span id='donation-button' style='float:right;width:40%'>
<!-- Beginning of paypal donation button -->
	  	<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----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-----END PKCS7-----
">
<input type="image" style='border:0' src="https://www.paypalobjects.com/WEBSCR-640-20110401-1/en_US/i/btn/btn_donate_LG.gif" name="submit" alt="PayPal - The safer, easier way to pay online!">
<img alt="" style='border:0' src="https://www.paypalobjects.com/WEBSCR-640-20110401-1/en_US/i/scr/pixel.gif" width="1" height="1">
</form>

<!-- End of paypal donation button -->
	  </span>
	  {/if}
              </div>


          


          </div>

          
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
            
          </div><!-- End of recent events -->

        
      <div id='chat-housing' style='height:250px;'>
        
		{include file="mini-chat.section.tpl"}

	  </div><!-- End of chat-housing -->



      </aside><!-- End of sidebar-column -->  
     
      
      <footer id='index-footer' class='navigation'>
        <div id='footer-top-bar'>
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
        <span style="display:none">True ninja do not use IE6.</span>
        <span style="display:none">Ask a geisha for "full service", get a free chopstick in the eye.</span>
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
        </div><!-- End of footer-top-bar -->
      </footer>
      
    
<!-- Validated as of Oct, 2009 -->

<!-- Version: {$version|escape} -->
