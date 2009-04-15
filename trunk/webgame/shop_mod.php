<?php
$page_title = "Purchasing";
$alive      = "true";
$private    = "true";
$quickstat  = "viewinv";

include "interface/header.php";
?>

<span class="brownHeading">Shop</span>

<p>
Shop: Prices subject to change.<br /><br />

<?php
$gold = getGold($username);

$item = in('item');
$quantity = intval(in('quantity'));

if ($item == "Fire Scroll")    { $current_item_cost = 200;}
if ($item == "Ice Scroll")     { $current_item_cost = 125;}
if ($item == "Shuriken")       { $current_item_cost = 75;}
if ($item == "Speed Scroll")   { $current_item_cost = 250;}
if ($item == "Stealth Scroll") { $current_item_cost = 300;}
if ($item == "Dim Mak")        { $current_item_cost = 10000;}

if (!$quantity || $quantity == 1){
  $quantity = 1;
  $grammar = " has";
} else {
  $grammar = "s have";
}

$current_item_cost*=$quantity;

if ($current_item_cost > $gold)
{
  echo "You can not afford this item.\n";
}
else
{
  additem($username,$item,$quantity);
    
  echo "$quantity ".$item.$grammar." been purchased.\n";

  subtractGold($username,$current_item_cost);
}

include "interface/footer.php";
?>

