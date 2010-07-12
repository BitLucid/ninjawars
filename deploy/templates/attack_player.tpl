<h1>Village</h1>

<div id='attack-player-page'>

    <h3>Locations</h3>
    <ul style='margin: .5em auto;text-align:center;font-size:1.3em;'>
{foreach name="looploc" from=$locations item="loc" key="idx"}
      <li style='padding-left:8px'>
      	<a href='{$loc.url|escape}'>
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
      <li><a href='{$npc.url|escape}' target='main'><img alt='' src='images/characters/{$npc.image|escape:'url'|escape}' style='width:25px;height:46px'> {$npc.name|escape}</a></li>
{/foreach}
  </ul>
      
  <hr>

  <p>
    To attack a ninja, use the <a href="list_all_players.php?hide=dead" target='main'>player list</a> or search for a ninja below.
  </p>

  <form id="player_search" action="list_all_players.php" method="get" name="player_search">
    <div>
      Search by Ninja Name or Rank
      <input id="searched" type="text" maxlength="50" name="searched" class="textField">
      <input id="hide" type="hidden" name="hide" value="dead">
      <button type="submit" value="Search for Ninja" class="formButton">Search for Ninja</button>
    </div>
  </form>

</div><!-- End of attack-player page container div -->
