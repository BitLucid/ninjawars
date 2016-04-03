<!-- This template is only used after login -->

    <!-- Version {$version|escape} -->
	<div id="logo-appended">
	  <a href="/">
        <img id='ninjawars-title-image' src='{cachebust file="/images/halfShuriken.png"}' title='Home' alt='Ninja Wars' width='100' height='100'>
	  </a>
	</div>

    <header id='index-header' class='clearfix'>

	  <div id='logo-placeholder'>
	    <!-- Spacer div for the main shuriken linkback logo -->
	    &nbsp;
	  </div>
    <div class='bars-container parent'>
  	  <div id='health-and-turns' class='various-bars child'>
  	  	<div id='barstats'>
  		  	<!-- Display the number bars for various char stats-->
  		  	<div id='health'>
  			  {include file="generic_bar.tpl" bar_percent=$player_info.hp_percent number=$player_info.health zero_word='Dead' number_of='Health' bar_color='#660000' title='Heal Yourself' action='/shrine/heal_and_resurrect'}<!-- #ee2520 -->
  		  	</div>
  		  	<div id='turns'>
  			  {include file="generic_bar.tpl" bar_percent=$player_info.turns_percent number=$player_info.turns zero_word='No Turns' number_of='Turns' bar_color='#003366' title='Speed Up' action='/item/self_use/amanita'}
  		  	</div>
  		  	<div id='kills'>
  			  {include file="generic_bar.tpl" bar_percent=$player_info.exp_percent number=$player_info.kills zero_word='No Kills' number_of='Kills' bar_color='#330066' title='View Stats' action='/stats'}<!-- #6612ee -->
  		  	</div>
  	  	</div>
  	  </div>
    </div>

    <div id='ninja-box'>
      <div class='text-info'>
        <div id='messages'>
            <div>
                <a target="main" id='message-inbox' href="/messages"><i class="fa fa-comments"></i><span class='unread-count'>{$unread_message_count}</span>
                </a>
            </div>
            <div>
              <a target='main' href='/events'>
                <i class="fa fa-star"></i>
              </a>
            </div>
        </div>
      </div>
      <div id='index-avatar'>
        {include file="gravatar.tpl" gurl=$ninja->avatarUrl()}
      </div>

      <div id='ninja-dropdown' class='bubble'>
        {include file="ninja.menu.tpl" ninja=$ninja}
      </div> <!-- end of #ninja-dropdown -->

    </div><!-- end of #ninja-box -->




      <div id='menu-bar' class='header-section'>
        <div id='reactive-panel'>
            <nav id='category-bar' class='navigation'>
              <ul>
                <li id='status-actions' class='self'>
                  <a href='/events' rel='nav' target='main' >
                    <img src='{cachebust file="/images/ninja_status_icon_50px.png"}' alt='' style='width:50px;height:51px'>Watch
                  </a>
                </li>
                <li id='combat-actions' class='combat'>
                  <a href='/enemies' rel='nav' target='main' title='Check up on your enemies and see who recently attacked you.'>
                    <img src='{cachebust file="/images/50pxShuriken.png"}' alt=''  style='width:50px;height:42px'>Fight
                  </a>
                </li>
                <li id='map-actions' class='map'>
                  <a href='/map' rel='nav' target='main' title='Travel to different locations on the Map.'>
                    <img src='{cachebust file="/images/pagodaIcon_60px.png"}' alt=''  style='width:60px;height:52px'>Move
                  </a>
                </li>
              </ul>
            </nav>
            <nav id='subcategory-bar' class='navigation' rel='nav'>
                <ul id='self-subcategory'>
                  <!--
                  <li><a href="/stats" rel='nav' target="main" title='Your ninja strength, level, profile, etc.'>Self</a></li>
                  -->
                  <li><a href="/skill" rel='nav' target="main" title='Your ninja skills &amp; abilities'>Skills</a></li>
                  <li><a href="/inventory" rel='nav' target="main" title='Your items and links to use them on yourself.'>Items</a></li>
                  <!-- Profile -->
                  <!-- Settings -->
                </ul>
                <ul id='combat-subcategory'>
                  <li><a href="/list" rel='nav' target="main" title='Ranked list of ninjas to attack.'>Ninjas</a></li>
                  <li><a href="/clan" rel='nav' target="main" title='Clans and your clan options.'>Clans</a></li>
                </ul>
                <ul id='map-subcategory'>
                  <li><a href="/shop" rel='nav' target="main" title='Spend your money to get weapons.'>Buy</a></li>
                  <li><a href="/work" rel='nav' target="main" title='Trade your turns to get money.'>Work</a></li>
                  <li><a href="/doshin" rel='nav' target="main" title='Hunt bounties for money.'>Hunt<img src="{cachebust file="/images/doshin.png"}" alt="" style='height:8px;width:8px'></a></li>
                </ul>
            </nav>
        </div><!-- End of reactive panel -->

      </div><!-- End of menu-bar -->



	  </header><!-- End of header -->


      <section id='core' class='clearfix'>
      <!-- Test stuff! -->
      <nav id='left-nav'>
      	<a id='skip-to-bottom' href='#index-footer'>&#x25bc;</a>
      </nav>

      <!-- MAIN COLUMN STARTS HERE -->
		  {include file="core.tpl"}
      <!-- Core Column ends here -->


      <!-- SIDEBAR COLUMN STARTS HERE -->
      <aside id='sidebar-column'>

        <div id='chat-housing'>
          {include file="mini-chat.section.tpl"}
        </div><!-- End of chat-housing -->

      </aside><!-- End of sidebar-column -->
      </section><!-- end of core-->


      <footer id='index-footer' class='navigation'>
        <!-- Stuff like catchphrases, links, and the author information -->
        {include file='linkbar_section.tpl'}
      </footer>

<!-- Version: {$version|escape} -->
