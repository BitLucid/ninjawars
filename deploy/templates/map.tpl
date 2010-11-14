<h1>Map</h1>

<div id='attack-player-page'>

    <ul style='margin: .5em auto;text-align:center;font-size:1.3em;'>
{foreach name="looploc" from=$locations item="loc" key="idx"}
      <li style='padding-left:8px'>
      	<a href='{$loc.url|escape}'>
	{if isset($loc.tile_image)}
	    <img src='/images/{$loc.tile_image}' alt='' style='max-width:100px;max-height:100px'>
	{/if}
	{if isset($loc.image)}
          <img src='/images/{$loc.image|escape:'url'|escape}' alt='' style='width:8px;height:8px'>
	{/if}
          {$loc.name|escape}
      	</a>
      </li>
{/foreach}
    </ul>
  
  <hr>
  
  <h3>Attack a citizen:</h3>
  <ul id='npc-list' style='margin: .5em auto;text-align:center;font-size:1.3em;'>
{foreach name="person" from=$npcs key="idx" item="npc"}
      <li><a href='npc.php?attacked=1&amp;victim={$npc.identity|escape}' target='main'><img alt='' src='images/characters/{$npc.image|escape:'url'|escape}' style='width:25px;height:46px'> {$npc.name|escape}</a></li>
{/foreach}
  </ul>

</div><!-- End of attack-player page container div -->
