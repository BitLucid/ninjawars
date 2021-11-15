    <!-- Version {$version|escape} -->

<style>
{literal}
.chat-show-hide-container {
    display: inline-block;
}
.content-button {
  background:transparent;
  border:none;
}
.navbar-fixed-top .chat-show-hide-container button{
  color: #9d9d9d;
  font-size: 3rem;
}
{/literal}
</style>

    <!-- Fixed navbar -->
<nav class="navbar navbar-inverse navbar-fixed-top">
      <a class="navbar-brand" data-toggle="collapse" data-target=".navbar-collapse"><img id='ninjawars-title-image' src='{cachebust file="/images/halfShuriken.png"}' title='Home' alt='Ninja Wars' width='30' height='30'></a>
      <div id="navbar" class="collapse navbar-collapse">

        <ul class="nav navbar-nav">
          <li><a href="/list" target="main"><i class='fa fa-star' aria-hidden='true'></i> Ninjas</a></li>
          <li><a href="/enemies" target="main"><i class='fa fa-bolt' aria-hidden='true'></i> Fight</a></li>
          <li><a href="/map" target="main"><i class='fa fa-map' aria-hidden='true'></i> Map</a></li>
          <li><a href="/inventory" target="main">Inventory</a></li>
          <li><a href="/skill" target="main">Skills</a></li>
          <li><a href="/clan" target="main"><i class='fa fa-users' aria-hidden='true'></i><span class='hidden-sm hidden-md'> Clans</span></a></li>
          <li class='hidden-sm'><a href="/shrine" target="main">⛩ Shrine</a></li>
          <li class='hidden-sm'><a href="/shop" target="main">石 Shop</a></li>
          <li class='hidden-sm hidden-md'><a href="/work" target="main"><i class="fab fa-pagelines"></i> Work</a></li>
          <li class='hidden-sm hidden-md'><a href="/doshin" target="main"><i class='fa fa-bullseye' aria-hidden='true'></i> <span class='hidden-md hidden-sm'>Hunt</span></a></li>
          <li><a href="/events" title='Events' target="main"><i class="far fa-clock" aria-hidden="true"></i> <span class='hidden-sm hidden-md'>Events</span></a></li>
          <li><a href="/messages" target="main"><i class='fa fa-envelope'></i> <span class='badge'>{$unread_message_count}</span></a></li>
        </ul>

        <div class='chat-show-hide-container'>
          <button id='chat-toggle' type='button' class='btn content-button'><i class='fa fa-comments'></i></button>
        </div>

        {if $ninja->id()}
          {include file="selfmenu.partial.tpl"}
        {/if}

      </div><!--/.nav-collapse -->


      <div class='health-container'>
          <div class='health-bar' data-json="{$ninja|@json_encode|escape|escape:'quotes' nofilter}"></div>
      </div>
    </nav>

    <!-- Begin page content -->
    <div class="container-fluid">

      <section id='core' class='clearfix'>
      <!-- <nav id='left-nav'>
      	<a id='skip-to-bottom' href='#index-footer'>&#x25bc;</a>
      </nav>
      -->

      <!-- MAIN COLUMN STARTS HERE -->
		  {include file="core.tpl"}
      <!-- Core Column ends here -->


      <!-- SIDEBAR COLUMN STARTS HERE -->
        {include file='aside.tpl'}
      <!-- End of sidebar-column -->
      </section><!-- end of core-->

<script src='/js/homepage.js'></script>
<!-- Version: {$version|escape} -->