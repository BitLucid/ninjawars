<?php
$alive      = false;
$private    = true;
$quickstat  = "viewinv";
$page_title = "Shop";

include "interface/header.php";

$description = "";
$in_purchase = in('purchase');
$in_quantity = in('quantity');
$item = in('item');

if ($in_purchase == 1)
{
  $gold = getGold($username);

  if ($item == "Fire Scroll")    { $current_item_cost = 175;}
  if ($item == "Ice Scroll")     { $current_item_cost = 125;}
  if ($item == "Shuriken")       { $current_item_cost = 50;}
  if ($item == "Speed Scroll")   { $current_item_cost = 225;}
  if ($item == "Stealth Scroll") { $current_item_cost = 150;}
  if ($item == "Dim Mak")        { $current_item_cost = 10000;}
  
  $quantity = intval($in_quantity);
  
  $grammar="";

  if (!$quantity || $quantity < 1)
    {
      $quantity = 1;
    }
  else if ($quantity > 1 && $item != "Shuriken")
    {
      $grammar = "s";
    }
  $current_item_cost = 0;
  $current_item_cost*=$quantity;
  
  if ($current_item_cost > $gold)
    {
      $description.="\"The total comes to $current_item_cost gold,\" the shopkeeper tells you.\n";
      $description.="<br /><br />\n";
      $description.="Unfortunately, you do not have that much gold.\n";
    }
  else
    {
      addItem($username,$item,$quantity);
      
      $description.="The shopkeeper hands over $quantity ".$item.$grammar.".\n";
      $description.="<br /><br />\n";
      $description.="\"Will you be needing anything else today?\" he asks you as he puts your gold in a safe.\n";
      
      subtractGold($username,$current_item_cost);
    }
}
else
{
  $description.="You enter the village shop and the shopkeeper greets you with a watchful eye.\n";
  $description.="<br /><br />\n";
  $description.="As you browse his wares he reminds you, \"All prices are subject to change.\"\n";
}
echo "<div class=\"brownTitle\">Shop</div>\n";

echo "<div class=\"description\">\n";
echo $description;
echo "</div>\n";
?>

<form id="shop_form" action="shop.php" method="post" name="shop_form">
<input id="purchase" type="hidden" value="1" name="purchase" />
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
  <input name="item" type="submit" value="Fire Scroll" class="shopButton" />
  </td>

  <td>
  Reduces HP
  </td>

  <td>
  $175
  </td>
  
  <td>
  <img src="images/scroll.png" />
</td>
</tr>
<tr>
  <td>
  <input name="item" type="submit" value="Ice Scroll" class="shopButton" />
  </td>

  <td>
  Reduces Turns
  </td>

  <td>
  $125
  </td>

  <td>
  <img src="images/scroll.png" />
  </td>
</tr>
<tr>
  <td>
  <input name="item" type="submit" value="Speed Scroll" class="shopButton" />
  </td>

  <td>
  Increases Turns
  </td>

  <td>
  $225
  </td>

  <td>
  <img src="images/scroll.png" />
  </td>
</tr>
<tr>
  <td>
  <input name="item" type="submit" value="Stealth Scroll" class="shopButton" />
  </td>

  <td>
  Stealths a Ninja(<a href="about.php#magic">*</a>)
  </td>

  <td>
  $150
  </td>

  <td>
  <img src="images/scroll.png" />
  </td>
</tr>
<tr>
  <td>
  <input name="item" type="submit" value="Shuriken" class="shopButton" />
  </td>

  <td>
  Reduces HP
  </td>

  <td>
  $50
  </td>

  <td>
  <img src="images/mini_star.png" />
  </td>
</tr>
<tr>
  <td colspan="3">
  How Many? <input id="quantity" type="text" size="3" maxlength="5" name="quantity" class="textField" />
  </td>
</tr>
</table>
</form>

<?php

include "interface/footer.php";
?>


