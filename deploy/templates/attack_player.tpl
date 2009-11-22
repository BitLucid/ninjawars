<h2>Village</h2>

<div id='attack-player-page'>

  {$locations}
  
  <hr>
  
  {$npcs}

  <hr>

  <p>
    To attack a ninja, use the <a href="list_all_players.php?hide=dead" target='main'>player list</a> or search for a ninja below.
  </p>

  <form id="player_search" action="list_all_players.php" method="get" name="player_search">
    <div>
      Search by Ninja Name or Rank
      <input id="searched" type="text" maxlength="50" name="searched" class="textField">
      <input id="hide" type="hidden" name="hide" value="dead">
      <input type="submit" value="Search for Ninja" class="formButton">
    </div>
  </form>

</div><!-- End of attack-player page container div -->
