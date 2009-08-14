<?php
$alive      = false;
$private    = true;
$quickstat  = "viewinv";
$page_title = "Shop";

include SERVER_ROOT."interface/header.php";

$description = "";
$in_purchase = in('purchase');
$in_quantity = in('quantity');
$item = in('item');
$grammar ="";
$username = get_username();
$gold = either(getGold($username), 0);
$current_item_cost = 0;
$quantity = intval($in_quantity);
if(!$quantity || $quantity < 1){
    $quantity = 1;
} else if ($quantity > 1 && $item != "Shuriken"){
    $grammar = "s";
}
$item_costs = array(
    /*"Dim Mak"=>10000,*/
    "Speed Scroll"=>225,
    "Fire Scroll"=>175,
    "Stealth Scroll"=>150,
    "Ice Scroll"=>125,
    "Shuriken"=>50,
);


if ($in_purchase == 1){
    $current_item_cost = either($item_costs[$item], 0);
    $current_item_cost*=$quantity;
  
  if ($current_item_cost > $gold){ // Not enough gold.
      $description.="<p>\"The total comes to $current_item_cost gold,\" the shopkeeper tells you.</p>";
      $description.="<p>Unfortunately, you do not have that much gold.</p>";
    } else { // Has enough gold.
      addItem($username,$item,$quantity);
      
      $description.="<p>The shopkeeper hands over $quantity ".$item.$grammar.".</p>";
      $description.="<p>\"Will you be needing anything else today?\" he asks you as he puts your gold in a safe.</p>";
      
      subtractGold($username,$current_item_cost);
    }
} else { // Default, before anything has been bought.
  $description.="<p>You enter the village shop and the shopkeeper greets you with a watchful eye.</p>";
  $description.="<p>As you browse his wares he says, \"Don't try anythin' you'd regret.\" and grins.</p>";
}
echo "<div class=\"brownTitle\">Shop</div>\n";

echo "<div class=\"description\">\n";
echo $description;
echo "</div>\n";
?>

<form id="shop_form" action="shop.php" method="post" name="shop_form">
<input id="purchase" type="hidden" value="1" name="purchase">
<table border="0">
<tr>
  <td>
  Item
  </td>

  <td>
  Description
  </td>

  <td>
  Cost
  </td>

  <td>
  Picture
  </td>
</tr>
<tr>
  <td>
  <input name="item" type="submit" value="Fire Scroll" class="shopButton">
  </td>

  <td>
  Reduces HP
  </td>

  <td>
  $175
  </td>
  
  <td>
  <img src="images/scroll.png">
</td>
</tr>
<tr>
  <td>
  <input name="item" type="submit" value="Ice Scroll" class="shopButton">
  </td>

  <td>
  Reduces Turns
  </td>

  <td>
  $125
  </td>

  <td>
  <img src="images/scroll.png">
  </td>
</tr>
<tr>
  <td>
  <input name="item" type="submit" value="Speed Scroll" class="shopButton">
  </td>

  <td>
  Increases Turns
  </td>

  <td>
  $225
  </td>

  <td>
  <img src="images/scroll.png">
  </td>
</tr>
<tr>
  <td>
  <input name="item" type="submit" value="Stealth Scroll" class="shopButton">
  </td>

  <td>
  Stealths a Ninja(<a href="about.php#magic">*</a>)
  </td>

  <td>
  $150
  </td>

  <td>
  <img src="images/scroll.png">
  </td>
</tr>
<tr>
  <td>
  <input name="item" type="submit" value="Shuriken" class="shopButton">
  </td>

  <td>
  Reduces HP
  </td>

  <td>
  $50
  </td>

  <td>
  <img src="images/mini_star.png">
  </td>
</tr>
<tr>
  <td colspan="3">
  How Many? <input id="quantity" type="text" size="3" maxlength="5" name="quantity" class="textField">
  </td>
</tr>
</table>
</form>

<?php

include SERVER_ROOT."interface/footer.php";
?>


