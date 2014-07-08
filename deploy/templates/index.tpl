
<!-- This template is only used after login -->

    <!-- Version {$version|escape} -->
	<div id="logo-appended">
	  <a href="/">
        <img id='ninjawars-title-image' src='images/halfShuriken.png' title='Home' alt='Ninja Wars' width='100' height='100'>
	  </a>
	</div>

    <header id='index-header' class='clearfix'>

	  <div id='logo-placeholder'>
	    <!-- Spacer div for the main shuriken linkback logo -->
	    &nbsp;
	  </div>
	  <div id='health-and-turns' class='various-bars' style='width:46%;display:inline-block;vertical-align:top;margin:.3% 15% .1em;'>
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
		      <img src='{$smarty.const.IMAGE_ROOT|escape}logout.png' alt='Logout' title='Logout of the game' style='height:60px;width:60px'>
		    </a>
		</div>

      <div id='menu-bar' class='header-section'>
        <div id='reactive-panel'>
            <nav id='category-bar' class='navigation'>
              <ul>
                <li id='status-actions' class='self'>
                  <a href='events.php' rel='nav' target='main' title='See messages about whether you were attacked or other events.'>
                    <img src='/images/ninja_status_icon_50px.png' alt='' style='width:50px;height:51px'>Watch
                  </a>
                </li>
                <li id='combat-actions' class='combat'>
                  <a href='enemies.php' rel='nav' target='main' title='Check up on your enemies and see who recently attacked you.'>
                    <img src='images/50pxShuriken.png' alt=''  style='width:50px;height:42px'>Fight
                  </a>
                </li>
                <li id='map-actions' class='map'>
                  <a href='map.php' rel='nav' target='main' title='Travel to different locations on the Map.'>                  
                    <img src='images/pagodaIcon_60px.png' alt=''  style='width:60px;height:52px'>Move
                  </a>
                </li>
              </ul>
            </nav>
            <nav id='subcategory-bar' class='navigation' rel='nav'>
                <ul id='self-subcategory'>
                  <li><a href="stats.php" rel='nav' target="main" title='Your ninja strength, level, profile, etc.'>Self</a></li>
                  <li><a href="skills.php" rel='nav' target="main" title='Your ninja skills &amp; abilities'>Skills</a></li>
                  <li><a href="inventory.php" rel='nav' target="main" title='Your items and links to use them on yourself.'>Items</a></li>
                  <!-- Profile -->
                  <!-- Settings -->
                </ul>
                <ul id='combat-subcategory'>
                  <li><a href="list.php" rel='nav' target="main" title='Ranked list of ninjas to attack.'>Ninjas</a></li>
                  <li><a href="clan.php" rel='nav' target="main" title='Clans and your clan options.'>Clans</a></li>
                </ul>
                <ul id='map-subcategory'>
                  <li><a href="shop.php" rel='nav' target="main" title='Spend your money to get weapons.'>Buy</a></li>
                  <li><a href="work.php" rel='nav' target="main" title='Trade your turns to get money.'>Work</a></li>
                  <li><a href="doshin_office.php" rel='nav' target="main" title='Hunt bounties for money.'>Hunt<img src="images/doshin.png" alt="" style='height:8px;width:8px'></a></li>
                </ul>
            </nav>
        </div><!-- End of reactive panel -->
        
      </div><!-- End of menu-bar -->


            
	  </header><!-- End of header -->
      
      
      <section id='core' class='clearfix'>
      
      <!-- Test stuff! -->
      <nav id='left-nav'>
      	&nbsp;
      	<a id='skip-to-bottom' href='#index-footer'>skip to<br>bottom</a>
      </nav>
      
      <!-- MAIN COLUMN STARTS HERE -->
		{include file="core.tpl"}
	  <!-- Core Column ends here -->


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
                  <a target="main" id='message-inbox' href="messages.php">Messages<img id='messages-icon' src='/images/icons/mono/commentblack32.png'  height=16 width=16 alt='' style='vertical-align:top'><span class='unread-count'>{$unread_message_count}</span>
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

    {if isset($show_news) and $show_news}
    <div id='news-housing' style='height:80px;margin-top:20px;'>
        
    {include file="mini-news.section.tpl"}

    </div><!-- End of news-housing -->
    {/if}
        
      <div id='chat-housing' style='height:250px;'>
        
		{include file="mini-chat.section.tpl"}

	  </div><!-- End of chat-housing -->



      </aside><!-- End of sidebar-column -->  
     
      </section><!-- end of core-->
      
      
      <footer id='index-footer' class='navigation'>
      
      <!-- Stuff like catchphrases, links, and the author information -->
      {include file='linkbar_section.tpl'}

      </footer>
      
<!-- Version: {$version|escape} -->
