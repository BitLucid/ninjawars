<?php
$alive      = false;
$private    = true;
$quickstat  = false;
$page_title = "Quickstats";

include_once("interface/header.php");
require_once(LIB_ROOT."specific/lib_status.php"); // Status alterations.

// *** Turning the header variables into variables for this page.
$section_only = in('section_only'); // Check whether it's an ajax section.
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

$low_health_css = '';
if($health<80){
    // Make health display red if it goes below 80.
    $low_health_css = " style='color:red;font-weight:bold;'";
}




if ($command != "viewinv") {
  echo "<table style=\"border: 0;\" class='quickstats player-stats'>\n";
  echo "<tr>\n";
  echo "  <td>\n";
  echo "  Health: \n";
  echo "  </td>\n";
  
  echo "  <td>\n";
  echo    "<span ".$low_health_css.">".$health."</span>\n";

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

  /*Comment out mail to speed up quickstats.
  $count = $sql->QueryItem("SELECT count(send_to) FROM mail WHERE send_to = '".$_SESSION['username']."' ");
  echo "<tr>\n";
  echo "  <td>\n";
  echo "  Mail: \n";
  echo "  </td>\n";
  echo "  <td>\n";
  echo    $count."<br />\n";
  echo "  </td>\n";
  echo "</tr>\n";
  */

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

// Write out a function call to update the login-bar's health display.
echo "<script language='javascript' type='text/javascript'>
        updateHealthBar('$players_health');
      </script>";

if(!$section_only){
    ?>
    </body>
    </html>
    <?php
}
