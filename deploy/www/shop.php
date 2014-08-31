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
$dirty_item 	   = in('item');
$item              = (strlen($dirty_item) > 2)? $dirty_item : null;
$item_id 		   = item_id_from_display_name($item);
$item_info        = positive_int($item_id)? item_info(item_id_from_display_name($item)) : null;
$item_identity = @$item_info['item_internal_name'];
$grammar           = "";
$username          = self_name();
$char_id           = self_char_id();
$gold              = get_gold($char_id);
$current_item_cost = 0;
$is_logged_in      = is_logged_in();


$setting_quantity = get_setting('items_quantity');

// Determine the quantity from input, or settings, or as a fallback, default of 1.
$quantity = whichever(positive_int($in_quantity), $setting_quantity, 1);

set_setting('items_quantity', $quantity);

if ($quantity > 1 && $item != "Shuriken") {
    // TODO: Change this to use the database plural field.
	$grammar = "s";
}

$item_costs = item_for_sale_costs(); // Pull the item info from the database.

$not_enough_gold = false;
$no_funny_business = false;

if(0>$quantity){ // Negative quantity requested
	$current_item_cost = 0;
	$no_funny_business = true;
} else { // Positive or zero quantity requested.
	if ($in_purchase == 1 && $item) {
		$current_item_cost  = first_value($item_costs[$item_identity]['item_cost'], 0);
		$current_item_cost = $current_item_cost * $quantity;

		if ($current_item_cost > $gold){ // Not enough gold.
		    $not_enough_gold = true;
		} elseif(!$char_id || !$item_identity || !$quantity){
			$no_funny_business = true;
		} else { // Has enough gold.
			try{
			add_item($char_id, $item_identity, $quantity);
			subtract_gold($char_id, $current_item_cost);
			} catch (Exception $e){
				$invalid_item = $e->getMessage();
				error_log('Invalid Item attempted :'.$invalid_item);
				$no_funny_business = true;
			}
			$gold = get_gold($char_id);
		}
	}
}

$parts = array('item_costs'=>$item_costs, 'description'=>$description, 'username'=>$username, 'gold'=>$gold,
    'current_item_cost'=>$current_item_cost, 'quantity'=>$quantity, 'item'=>$item, 'grammar'=>$grammar, 'is_logged_in'=>$is_logged_in,
    'in_purchase'=>$in_purchase, 'not_enough_gold'=>$not_enough_gold, 'no_funny_business'=>$no_funny_business);

display_page(
	'shop.tpl'	// *** Main Template ***
	, 'Shop'	// *** Page Title ***
	, $parts	// *** Page Variables ***
	, array(	// *** Page Options ***
		'quickstat' => 'viewinv'
	)
);

} // End of no display error.
