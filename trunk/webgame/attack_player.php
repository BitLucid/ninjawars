<?php
$private    = true;
$alive      = false;
$page_title = "Attack";
$quickstat  = "player";

include "interface/header.php";
?>
  
<span class="brownHeading">Attack</span>

<br /><br />

<p>
To attack a ninja, use the <a href="list_all_players.php?hide=dead">player list</a> or search for a ninja below.</a>
</p>

<form id="player_search" action="list_all_players.php" method="get" name="player_search">
Search by Ninja Name or Rank
<input id="searched" type="text" maxlength="50" name="searched" class="textField" />
<input id="hide" type="hidden" name="hide" value="dead" />
<input type="submit" value="Search for Ninja" class="formButton" />
</form>

<hr />

<br />

Attack Non-Player Character:
<br />
<br />
<a href="attack_npc.php?attacked=1&victim=villager">Villager</a>
<br />
<br />
<a href="attack_npc.php?attacked=1&victim=merchant">Merchant</a>
<br />
<br />
<a href="attack_npc.php?attacked=1&victim=thief">Thief</a>
<br />
<br />
<a href="attack_npc.php?attacked=1&victim=guard">Emperor's Guard</a>
<br />
<br />
<a href="attack_npc.php?attacked=1&victim=samurai">Samurai</a>

<?php
include "interface/footer.php";
?>
