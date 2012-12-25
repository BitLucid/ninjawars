<h1>Map</h1>

<div id='attack-player-page'>

{include file='nodes.tpl' nodes=$nodes}
  
  <hr>
  
  <h3>Attack a:</h3>
  <ul id='npc-list' style='margin: .5em auto;text-align:center;font-size:1.3em;'>
{foreach name="person" from=$npcs key="idx" item="npc"}
      <li><a href='npc.php?attacked=1&amp;victim={$npc.identity|escape}' target='main'><img alt='' src='images/characters/{$npc.image|escape:'url'|escape}' style='width:25px;height:46px'> {$npc.name|escape}</a></li>
{/foreach}
{foreach name="creatures" from=$other_npcs key="idx" item="npc"}
      <li><a href='npc.php?attacked=1&amp;victim={$idx|escape}' target='main'>{if $npc.img}<img alt='' src='images/characters/{$npc.img|escape:'url'|escape}' style='max-width:50px;max-height:50px'>{else}<span style='width:25px;height:46px'>&#9733;</span>{/if} {$npc.name|escape}</a></li>
{/foreach}
  </ul>
  
  
{if $show_ad eq 1}
<!-- This particular ad is here mainly to focus the targeting of the advertising to more nw related topics. -->

    <!-- Google Ad -->
    <script type="text/javascript"><!--
    google_ad_client = "pub-9488510237149880";
    /* 300x250, created 12/17/09 */
    google_ad_slot = "9563671390";
    google_ad_width = 300;
    google_ad_height = 250;
    //-->
    </script>
    <script type="text/javascript" src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
    </script>
{/if}

</div><!-- End of attack-player page container div -->
