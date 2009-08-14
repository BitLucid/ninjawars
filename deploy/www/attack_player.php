<?php
$private    = true;
$alive      = false;
$page_title = "Combat";
$quickstat  = "player";

include SERVER_ROOT."interface/header.php";
?>
<div id='attack-player-page'>
<div class="brownHeading">Combat</div>

<p>
To attack a ninja, use the <a href="list_all_players.php?hide=dead">player list</a> or search for a ninja below.
</p>

<form id="player_search" action="list_all_players.php" method="get" name="player_search">
	Search by Ninja Name or Rank
	<input id="searched" type="text" maxlength="50" name="searched" class="textField">
	<input id="hide" type="hidden" name="hide" value="dead">
	<input type="submit" value="Search for Ninja" class="formButton">
</form>

<hr>

<h3>Attack a citizen:</h3>
<ul>
  <li>
	<a href="attack_npc.php?attacked=1&victim=villager">Villager</a>
  </li>
  <li>
<a href="attack_npc.php?attacked=1&victim=merchant">Merchant</a>
  </li>
  <li>
<a href="attack_npc.php?attacked=1&victim=thief">Thief</a>
  </li>
  <li>
<a href="attack_npc.php?attacked=1&victim=guard">Emperor's Guard</a>
  </li>
  <li>
<a href="attack_npc.php?attacked=1&victim=samurai">Samurai</a>
  </li>
</ul>

</div><!-- End of attack-player page container div -->

<?php
include SERVER_ROOT."interface/footer.php";
?>
