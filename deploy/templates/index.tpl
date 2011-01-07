    <!-- Version {$version|escape} -->
{literal}
      <script type="text/javascript">
	if (parent.frames.length != 0) {
		location.href = "attack_player.php";
	}
      </script>
    <style type="text/css">
    </style>
      
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
        <img id='ninjawars-title-image' src='images/halfShuriken.png' alt='Ninja Wars' width='100' height='100'>
	  </a>
	</div>

    <div class='header'>

	  <div id='logo-placeholder'>
	    <!-- Spacer div for the main shuriken linkback logo -->
	    &nbsp;
	  </div>
	  <div id='health-and-turns' class='various-bars' style='width:46%;display:inline-block;vertical-align:top;margin:0 15% .3em;'>
	  	<div id='barstats' style='width:100%;display:none;height:5em'>
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
		      <img src='{$smarty.const.IMAGE_ROOT|escape}logoutTriangle.png' alt='Logout' title='Leave the game' style='height:70px;width:70px'>
		    </a>
		</div>

      <div id='menu-bar' class='header-section'>
        <div id='reactive-panel'>
            <script type='text/javascript'>
            {literal}
            $(document).ready(function(){
            	// Hide the subcats initially.
            	$('#combat-subcategory, #self-subcategory, #map-subcategory').hide();
            	
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
            {/literal}
            </script>
            <div id='category-bar'>
              <ul>
                <li id='status-actions' class='self'>
                  <a href='events.php' target='main' title='See messages about whether you were attacked or other events.'>
                    <img src='/images/ninja_status_icon_50px.png' alt='' style='width:50px;height:51px'>Status
                  </a>
                </li>
                <li id='combat-actions' class='combat'>
                  <a href='enemies.php' target='main' title='Check up on your enemies and see who recently attacked you.'>
                    <img src='images/50pxShuriken.png' alt=''  style='width:50px;height:42px'>Combat
                  </a>
                </li>
                <li id='map-actions' class='map'>
                  <a href='map.php' target='main' title='Travel to different locations on the Map.'>                  
                    <img src='images/pagodaIcon_60px.png' alt=''  style='width:60px;height:52px'>Map
                  </a>
                </li>
              </ul>
            </div>
            <div id='subcategory-bar'>
                <ul id='self-subcategory'>
                  <li><a href="stats.php" target="main" title='Your ninja strength, level, profile, etc.'>Self</a></li>
                  <li><a href="skills.php" target="main" title='Your ninja skills &amp; abilities'>Skills</a></li>
                  <li><a href="inventory.php" target="main" title='Your items and links to use them on yourself.'>Items</a></li>
                  <!-- Profile -->
                  <!-- Settings -->
                </ul>
                <ul id='combat-subcategory'>
                  <li><a href="list.php" target="main" title='Ranked list of ninjas to attack.'>Ninja</a></li>
                  <li><a href="clan.php" target="main" title='Clans and your clan options.'>Clans</a></li>
                  <li><a href="duel.php" target="main" title="Today's Duels">Rumors</a></li>
                </ul>
                <ul id='map-subcategory'>
                  <li><a href="shop.php" target="main" title='Spend your money to get weapons.'>Shop</a></li>
                  <li><a href="work.php" target="main" title='Trade your turns to get money.'>Work</a></li>
                  <li><a href="doshin_office.php" target="main" title='Hunt bounties for money.'>Doshin <img src="images/doshin.png" alt="" style='height:8px;width:8px'></a></li>
                </ul>
            </div>
        </div><!-- End of reactive panel -->
        
      </div><!-- End of menu-bar -->


            
	  </div><!-- End of header -->
      
      
      
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
      <div id='sidebar-column'>
            <div>
                <a target="main" href="player.php?player_id={$user_id|escape:'url'|escape}" title='Display your ninja information'>
                	<strong class='char-name'>{$username|escape}</strong>
                </a>
            </div>
            
            {*
            <div id='logged-in-bar'>
                <div>
                  <span id='health-status'> </span>
                </div>
            </div>
            *}
            
            {*
            // This is to be replaced by info bars in the header.
            
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
          *}
          
          
          {*
          
          // Actions are no longer needed as such, since they're mostly fulfilled by the generic bars.
          
          <div id="actions" class="boxes active">
            <div class="box-title">
              <a id='show-hide-actions-menu' class="show-hide-link"><!-- jQuery show/hide -->
                Actions
              </a>
            </div>
            <ul class="basemenu" id="actions-menu"><!-- Id used by show hide jquery -->
              <li id='heal-link'>
                <a href="shrine_mod.php?heal_and_resurrect=1" target="main">
                    <img src='images/shrine.png' alt=''>Heal
				</a>
              </li>
              <li>
                <ul class="submenu">
                  <li>
                    <a href="inventory_mod.php?item=smokebomb&amp;selfTarget=1"
                     target="main">Stealth</a>
                  </li>
                  <li>
                    <a href="inventory_mod.php?item=amanita&amp;selfTarget=1" 
                    target="main">Speed</a>
                  </li>
                </ul>
              </li>
            </ul>
          </div>
          
          *}


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


      </div><!-- End of sidebar-column -->  
     
      
      
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
      </div>
      
    
<!-- Validated as of Oct, 2009 -->

<!-- Version: {$version|escape} -->
