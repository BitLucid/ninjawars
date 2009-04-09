<?php
$page_title = "Quest: Thieves Guild";
$alive      = true;
$private    = true;
$quickstat  = "player";

include "interface/header.php";
?>

<span class="brownHeading">Quest: Thieves Guild</span>

<p>
<?php
  $thief_gold = $sql->QueryItem("SELECT gold FROM guild WHERE guild_name = 'thief'");

  echo "Thieves roam these hills late at night.<br />\n";
  echo "They will steal gold from any living Ninja under level 5.<br /><br />\n";
  echo "Their current treasury is rumored to be <span style=\"font-weight: bold;\">$thief_gold</span> gold!<br />\n";
  echo "<br /><br />Under Construction - The cave entrance to Thief Guild is blocked you may not pass.<br /><br />\n";
?>

<hr />

<br /><br />

Inquiries about future quests ?<a href="mailto:Admin@ninjawars.net">Admin Ninja Lord</a>

<?php
include "interface/footer.php";
?>

