<?php
require_once(LIB_ROOT."specific/lib_inventory.php");

$private    = false;
$alive      = false;

if ($error = init($private, $alive)) {
	display_error($error);
} else {

$description       = "";
$in_purchase       = in('purchase');
$in_quantity       = in('quantity');
$item              = in('item');
$item_internal_name = item_internal_from_display($item);
$grammar           = "";
$username          = get_char_name();
$gold              = first_value(getGold($username), 0);
$current_item_cost = 0;
$quantity          = intval($in_quantity);
$is_logged_in      = is_logged_in();


if (!$quantity || $quantity < 1) {
	$quantity = 1;
} else if ($quantity > 1 && $item != "Shuriken") {
	$grammar = "s";
}

$item_costs = item_for_sale_costs(); // Pull the item info from the database.

$not_enough_gold = false;


if ($in_purchase == 1 && $item) {
	$current_item_cost  = first_value($item_costs[$item_internal_name]['item_cost'], 0);
	$current_item_cost *= $quantity;

	if ($current_item_cost > $gold){ // Not enough gold.
	    $not_enough_gold = true;
	} else { // Has enough gold.
		addItem($username, $item, $quantity);
		subtractGold($username, $current_item_cost);

	}
}

$parts = array('item_costs'=>$item_costs, 'description'=>$description, 'username'=>$username, 'gold'=>$gold,
    'current_item_cost'=>$current_item_cost, 'quantity'=>$quantity, 'item'=>$item, 'grammar'=>$grammar, 'is_logged_in'=>$is_logged_in,
    'in_purchase'=>$in_purchase, 'not_enough_gold'=>$not_enough_gold);

display_page(
	'shop.tpl'	// *** Main Template ***
	, 'Shop'	// *** Page Title ***
	, $parts	// *** Page Variables ***
	, array(	// *** Page Options ***
		'quickstat' => 'viewinv'
	)
);
}
?>
