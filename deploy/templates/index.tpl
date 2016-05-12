    <!-- Version {$version|escape} -->

    <!-- Fixed navbar -->
    <nav class="navbar navbar-default navbar-fixed-top">
      <div class="container">
        <div id="logo-appended">
          <a href="/">
            <img id='ninjawars-title-image' src='{cachebust file="/images/halfShuriken.png"}' title='Home' alt='Ninja Wars' width='100' height='100'>
          </a>
        </div>
        <div class="navbar-header">
          <a class="navbar-brand" href="/">NinjaWars</a>
        </div>
        <div id="navbar" class="collapse navbar-collapse">
          <ul class="nav navbar-nav">
            <li><a href="/messages" target="main">Inbox ({$unread_message_count})</a></li>
            <li><a href="/map" target="main">Map</a></li>
            <li><a href="/events" target="main">Events</a></li>
            <li><a href="/enemies" target="main">Enemies</a></li>
            <li><a href="/list" target="main">Ninja</a></li>
            <li><a href="/inventory" target="main">Inventory</a></li>
            <li><a href="/skills" target="main">Skills</a></li>
            <li><a href="/clan" target="main">Clans</a></li>
            <li><a href="/shop" target="main">Shop</a></li>
            <li><a href="/work" target="main">Work</a></li>
			<li><a href="/doshin" target="main">Hunt<img src="{cachebust file="/images/doshin.png"}" alt="" style='height:8px;width:8px'></a></li>
          </ul>
        </div><!--/.nav-collapse -->
      </div>
    </nav>

    <!-- Begin page content -->
    <div class="container-fluid">

	  <div class="page-header">
        <div id='ninja-box'>
          <div class='text-info'>
          </div>
          <div id='index-avatar'>
{include file="gravatar.tpl" gurl=$ninja->avatarUrl()}
          </div>

          <div id='ninja-dropdown' class='bubble'>
{include file="ninja.menu.tpl" ninja=$ninja}
          </div> <!-- end of #ninja-dropdown -->
        </div><!-- end of #ninja-box -->
      </div>


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
