    <!-- Version {$version|escape} -->

    <!-- Fixed navbar -->
    <nav class="navbar navbar-inverse navbar-fixed-top">
      <div id="logo-appended">
        <a href="/">
          <!-- <img id='ninjawars-title-image' src='{cachebust file="/images/halfShuriken.png"}' title='Home' alt='Ninja Wars' width='100' height='100'>-->
        </a>
      </div>
      <div class="navbar-header">
        <a class="navbar-brand">NinjaWars</a>
      </div>
      <div id="navbar" class="collapse navbar-collapse">
        <ul class="nav navbar-nav">
          <li><a href="/messages" target="main">Inbox <span class='badge'>{$unread_message_count}</span></a></li>
          <li><a href="/events" target="main"><i class="fa fa-star-o" aria-hidden="true"></i>Events</a></li>
          <li><a href="/map" target="main">Map</a></li>
          <li><a href="/enemies" target="main">Fight</a></li>
          <li><a href="/list" target="main">Ninja</a></li>
          <li><a href="/inventory" target="main">Inventory</a></li>
          <li><a href="/skill" target="main">Skills</a></li>
          <li><a href="/clan" target="main">Clans</a></li>
          <li><a href="/shop" target="main">Shop</a></li>
          <li><a href="/work" target="main">Work</a></li>
          <li><a href="/doshin" target="main">Hunt</i></a></li>
        </ul>

{if $ninja->id()}
<div class="dropdown btn-group">
  <!-- Link or button to toggle dropdown -->
  <span id='index-avatar' class='dropdown-toggle inline-block' data-toggle="dropdown">
    {include file="gravatar.tpl" gurl=$ninja->avatarUrl()}
    <b class="caret"></b>
  </span>
  <ul class="dropdown-menu dropdown-inverse" role="menu" aria-labelledby="dLabel">
    <li><a class='ninja-name' target="main" href="/player?player_id={$ninja->id()|escape:'url'|escape}" title='Display your ninja information' tabindex="-1">
      <strong class='char-name'>{$ninja->name()|escape}</strong>
    </a></li>
    <li><span class='ninja-level text-muted'>Level {$ninja->level|escape}</span></li>
    <li><a href='/stats' target='main' title='Your ninja stats, level, info, etc.'><i class="fa fa-heart" tabindex="-1"></i> Ninja Stats</a></li>
    {if $clan}
    <li><a href="/clan/view?clan_id={$clan->id|escape}" target='main' title='Your clan members and clan chat' tabindex="-1"><i class='fa fa-users'></i> My Clan</a></li>
    {/if}
    <li class="divider"></li>
    <li><a href="/account" target="main" title='Your player account info, email, password, etc.' tabindex="-1"><i class="fa fa-gear"></i> Account Info</a></li>
    <li class="divider"></li>
    <li><a target='main' href='/intro'><i class="fa fa-question-circle" tabindex="-1"></i> Intro Guide</a></li>
    <li>
      <form method='post' action='/logout'>
        <input type='submit' name ='logout' value='Logout' class='btn btn-default'>
      </form>
    </li>
  </ul>
  <!-- Recent Events count and target will get put in here via javascript -->
  <div id='recent-events' class="boxes active" style='display:none'>
    <div>
      <a target='main' id='recent-event-attacked-by' href='/events' title='View events'>You weren't recently in combat</a> with <a id='view-event-char' target='main' href='#' title="View a player's profile">anyone</a>.
    </div>
  </div><!-- End of recent events -->
</div><!-- end of dropdown -->
{/if}


      </div><!--/.nav-collapse -->
    </nav>

    <!-- Begin page content -->
    <div class="container-fluid">

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

<!-- Version: {$version|escape} -->
