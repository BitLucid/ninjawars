<?php
$alive      = false;
$private    = true;
$quickstat  = false;
$page_title = "Quickstats";

include_once("interface/header.php");
require_once(LIB_ROOT."func/lib_status_output_list.php");

// *** Turning the header variables into variables for this page.
$command  = in('command');
$health   = $players_health;
$strength = $players_strength;
$gold     = $players_gold;
$kills    = $players_kills;
$turns    = $players_turns;
$level    = $players_level;
$class    = $players_class;
$bounty   = $players_bounty;
$status   = $players_status;  //The status variable is an array, of course.


//$member   = $sql->QueryItem("SELECT member FROM players WHERE uname = '$username'");  //UNUSED IN PAGE
//$clan   = $players_clan;    //UNUSED IN PAGE

if ($command != "viewinv") {
  echo "<table style=\"border: 0;\" class='quickstats player-stats'>\n";
  echo "<tr>\n";
  echo "  <td>\n";
  echo "  Health: \n";
  echo "  </td>\n";
  
  echo "  <td>\n";
  if ($health<80) {  // Makes your health red if you go below 80 hitpoints.
	echo "<span style=\"color:red;font-weight:bold;\">\n";  
  }
  echo    $health."\n";
  if ($health<80) {
	echo "</span>\n";  
  }    

  echo "  </td>\n";
  echo "</tr>\n";
  echo "<tr>\n";
  echo "  <td>\n";
  echo "  Status: \n";
  echo "  </td>\n";
  
  echo "  <td>\n";
  
	$status_output_list = status_output_list($status_array, $username);
	echo $status_output_list;
   

  
  echo "  </td>\n";
  echo "</tr>\n";
  echo "<tr>\n";
  echo "  <td>\n";
  echo "  Turns: \n";
  echo "  </td>\n";

  echo "  <td>\n";
  echo    $turns."\n";
  echo "  </td>\n";
  echo "</tr>\n";
  echo "<tr>\n";
  echo "  <td>\n";
  echo "  Gold: \n";
  echo "  </td>\n";

  echo "  <td>\n";
  echo    $gold."\n";
  echo "  </td>\n";
  echo "</tr>\n";
  echo "<tr>\n";
  echo "  <td>\n";
  echo "  Bounty: \n";
  echo "  </td>\n";

  echo "  <td>\n";
  echo    $bounty."\n";
  echo "  </td>\n";
  echo "</tr>\n";

  $count = $sql->QueryItem("SELECT count(send_to) FROM mail WHERE send_to = '".$_SESSION['username']."' ");
  echo "<tr>\n";
  echo "  <td>\n";
  echo "  Mail: \n";
  echo "  </td>\n";
  echo "  <td>\n";
  echo    $count."<br />\n";
  echo "  </td>\n";
  echo "</tr>\n";

  echo "</table>\n";
}
else if ($command == "viewinv") {
  $sql->Query("SELECT item, amount FROM inventory WHERE owner = '".$_SESSION['username']."' ORDER BY item");
  foreach($sql->FetchAll() AS $loopItem) {
      echo "<table style=\"border: 0;\" class='quickstats inventory'>\n";
      echo "<tr>\n";
      echo "  <td>\n";
      echo "  ".$loopItem['item'].": \n";
      echo "  </td>\n";
      echo "  <td>\n";
      echo    $loopItem['amount']."<br />\n";
      echo "  </td>\n";
      echo "</tr>\n";
  }

  echo "<tr>\n";
  echo "  <td>\n";
  echo "Gold: \n";
  echo "  </td>\n";
  echo "  <td>\n";
  echo    $players_gold." <br />\n";
  echo "  </td>\n";
  echo "</tr>\n";
  echo "</table>\n";
}

?>
</body>
</html>




