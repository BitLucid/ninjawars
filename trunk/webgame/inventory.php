<?php
$private    = true;
$alive      = false;
$quickstat  = "viewinv";
$page_title = "Your Inventory";

include "interface/header.php";
?>

<span class="brownHeading">Your Inventory</span>

<p>

<?php
$sql->Query("SELECT amount AS c, item FROM inventory WHERE owner = '$username' GROUP BY item, amount");

if ($sql->rows == 0) {
  echo "You have no items, to buy some, visit the <a href=\"shop.php\">shop</a>\n";
} else {
  $items['Shuriken']       = 0;
  $items['Stealth Scroll'] = 0;
  $items['Fire Scroll']    = 0;
  $items['Ice Scroll']     = 0;
  $items['Speed Scroll']   = 0;
  $items['Dim Mak'] = 0;

	while ($data = $sql->Fetch()) {
      $items[$data['item']] = $data['c'];
    }

  echo "Click a linked item to use it on yourself.<br /><br />\n";

  echo "<table style=\"width: 150;\">\n";
  if ($items['Speed Scroll'] > 0) {
      echo "<tr>\n";
      echo "  <td>\n";
      echo "  <a href=\"inventory_mod.php?item=Speed%20Scroll&target=$username&link_back=inventory\">Speed Scrolls</a>: \n";
      echo "  </td>\n";
      
      echo "  <td>\n";
      echo    $items['Speed Scroll']."\n";
      echo "  </td>\n";
      echo "</tr>\n";
    }
  if ($items['Stealth Scroll'] > 0) {
      echo "<tr>\n";
      echo "  <td>\n";
      echo "  <a href=\"inventory_mod.php?item=Stealth%20Scroll&target=$username&link_back=inventory\">Stealth Scrolls</a>: \n";
      echo "  </td>\n";
      
      echo "  <td>\n";
      echo    $items['Stealth Scroll']."\n";
      echo "  </td>\n";
      echo "</tr>\n";
    }
  if ($items['Shuriken'] > 0) {
      echo "<tr>\n";
      echo "  <td>\n";
      echo "  Shuriken: \n";
      echo "  </td>\n";
      
      echo "  <td>\n";
      echo    $items['Shuriken']."\n";
      echo "  </td>\n";
      echo "</tr>\n";
    }
  if ($items['Fire Scroll'] > 0) {
      echo "<tr>\n";
      echo "  <td>\n";
      echo "  Fire Scrolls: \n";
      echo "  </td>\n";
      
      echo "  <td>\n";
      echo    $items['Fire Scroll']."\n";
      echo "  </td>\n";
      echo "</tr>\n";
    }
  if ($items['Ice Scroll'] > 0) {
      echo "<tr>\n";
      echo "  <td>\n";
      echo "  Ice Scrolls: \n";
      echo "  </td>\n";
      
      echo "  <td>\n";
      echo    $items['Ice Scroll']."\n";
      echo "  </td>\n";
      echo "</tr>\n";
    }
	if ($items['Dim Mak'] > 0) {
      echo "<tr>\n";
      echo "  <td>\n";
      echo "  Dim Mak: \n";
      echo "  </td>\n";
      
      echo "  <td>\n";
      echo    $items['Dim Mak']."\n";
      echo "  </td>\n";
      echo "</tr>\n";
    }
  echo "</table>\n";
}
?>
<br /><br />
<a href="list_all_players.php?hide=dead">Use an Item on a ninja?</a>
<form id=\"player_search\" action="list_all_players.php" method="get" name=\"player_search\">
<input id="searched" type="text" maxlength="50" name="searched" class="textField" />
<input id="hide" type="hidden" name="hide" value="dead" />
<input type="submit" value="Search for Ninja" class="formButton" />
</form>

<br />
Current gold: <?echo getGold($username);?>
<hr />

</p>

<?php
include "interface/footer.php";
?>
