<?php
require_once(LIB_ROOT."control/lib_inventory.php");

$private    = false;
$alive      = true;

if ($error = init($private, $alive)) {
	display_error($error);
} else {

$description       = "";
$in_purchase       = in('purchase');
$in_quantity       = in('quantity');
$item              = in('item');
$item_info        = item_info(item_id_from_display_name($item));
$item_identity = $item_info['item_internal_name'];
$grammar           = "";
$username          = get_char_name();
$char_id           = get_char_id();
$gold              = get_gold($char_id);
$current_item_cost = 0;
$is_logged_in      = is_logged_in();


$setting_quantity = get_setting('items_quantity');

// Determine the quantity from input, or settings, or as a fallback, default of 1.
$quantity = (!intval($in_quantity) || intval($in_quantity) < 1) ? 
        ($setting_quantity? $setting_quantity : 1) : 
        intval($in_quantity);

set_setting('items_quantity', $quantity);

if ($quantity > 1 && $item != "Shuriken") {
    // TODO: Change this to use the database plural field.
	$grammar = "s";
}

$item_costs = item_for_sale_costs(); // Pull the item info from the database.

$not_enough_gold = false;


if ($in_purchase == 1 && $item) {
	$current_item_cost  = first_value($item_costs[$item_identity]['item_cost'], 0);
	$current_item_cost *= $quantity;

	if ($current_item_cost > $gold){ // Not enough gold.
	    $not_enough_gold = true;
	} else { // Has enough gold.
		add_item($char_id, $item_identity, $quantity);
		subtract_gold($char_id, $current_item_cost);

		$gold = get_gold($char_id);

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
