<?php
$private    = true;
$alive      = false;
$quickstat  = "viewinv";
$page_title = "Your Inventory";

include SERVER_ROOT."interface/header.php";
?>

<span class="brownHeading">Your Inventory</span>

<p>

<?php
$sql->Query("SELECT amount AS c, item FROM inventory WHERE owner = '$username' GROUP BY item, amount");

if ($sql->rows == 0) {
	echo "You have no items, to buy some, visit the <a href=\"shop.php\">shop</a>\n";
} else {
	$items['Speed Scroll']   = 0;
	$items['Stealth Scroll'] = 0;
	$items['Shuriken']       = 0;
	$items['Fire Scroll']    = 0;
	$items['Ice Scroll']     = 0;
	$items['Dim Mak']        = 0;

	$itemData = array(
		'Speed Scroll' => array(
			'codename'   => 'Speed Scroll'
			, 'display'  => 'Speed Scrolls'
		)
		, 'Stealth Scroll' => array(
			'codename'   => 'Stealth Scroll'
			, 'display'  => 'Stealth Scrolls'
		)
		, 'Shuriken' => array(
			'display'  => 'Shuriken'
		)
		, 'Fire Scroll' => array(
			'display'  => 'Fire Scrolls'
		)
		, 'Ice Scroll' => array(
			'display'  => 'Ice Scrolls'
		)
		, 'Dim Mak' => array(
			'display'  => 'Dim Mak'
		)
	);

	while ($data = $sql->Fetch()) {
		$items[$data['item']] = $data['c'];
	}

	echo "Click a linked item to use it on yourself.<br><br>\n";

	echo "<table style=\"width: 150;\">\n";

	foreach ($items AS $itemName=>$amount) {
		if ($amount > 0) {
			echo "<tr>\n";
			echo "  <td>\n    ";

			if (array_key_exists('codename', $itemData[$itemName]))
			{
				echo "<a href=\"inventory_mod.php?item=".urlencode($itemData[$itemName]['codename'])."&amp;selfTarget=1&amp;target=$username&amp;link_back=inventory\">";
			}

			echo $itemData[$itemName]['display'];

			if (array_key_exists('codename', $itemData[$itemName]))
			{
				echo "</a>";
			}

			echo ":\n  </td>\n";

			echo "  <td>\n";
			echo    $amount."\n";
			echo "  </td>\n";
			echo "</tr>\n";
		}
	}

	echo "</table>\n";
}
?>
  <br><br>
  <a href="list_all_players.php?hide=dead">Use an Item on a ninja?</a>
  <form id="player_search" action="list_all_players.php" method="get" name="player_search">
    <div>
      <input id="searched" type="text" maxlength="50" name="searched" class="textField">
      <input id="hide" type="hidden" name="hide" value="dead">
      <input type="submit" value="Search for Ninja" class="formButton">
    </div>
  </form>

  <br>
  Current gold: <?php echo getGold($username);?>
  <hr>

</p>

<?php
include SERVER_ROOT."interface/footer.php";
?>
