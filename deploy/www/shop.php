<?php
$alive      = false;
$private    = false;
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



$parts = get_certain_vars(get_defined_vars(), array());
echo render_template('shop.tpl', $parts); // Non-logged in template.

include SERVER_ROOT."interface/footer.php";
?>
