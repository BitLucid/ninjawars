<?php
$private   = true;
$alive     = false;

if ($error = init($private, $alive)) {
	display_error($error);
} else {

$username = get_username();
$user_id = get_user_id();
$sql = "SELECT amount AS count, item AS name FROM inventory WHERE owner = :owner";
$inv_counts = query_resultset($sql, array(':owner'=>array($user_id, PDO::PARAM_INT)));
$gold = getGold($username);

// TODO: move this to a more standard location so that it can be used, for example, in the shop.
function standard_items() {
	// Codename means it can have a link to be used, apparently...

	return array(
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
		, 'Ginseng Root' => array(
			'codename'   => 'Ginseng Root'
			, 'display'  => 'Ginseng Roots'
		)
		, 'Tiger Salve' => array(
			'codename'   => 'Tiger Salve'
			, 'display'  => 'Tiger Salves'
		)
	);
}

if ($inv_counts) {
	$standard_items = standard_items();

	// Make the information into a single, trivially usable, array.
	foreach ($inv_counts as $item_info) {
		$l_name  = $item_info['name'];
		$l_count = $item_info['count'];
		if (isset($standard_items[ucfirst($l_name)]) && isset($l_count)) {
			// If a type of item exists and has a non-zero count, join the array of it's count with it's standard info.
			$inventory[$l_name] = array('count'=>$l_count) + $standard_items[$l_name];
		}
	}
} else {
	$inventory = false;
}
//debug($standard_items);
//debug($inventory);

display_page(
	'inventory.tpl'
	, 'Your Inventory'
	, array(
		'gold'        => $gold
		, 'inventory' => $inventory
		, 'username'  => $username
	)
	, array(
		'quickstat' => 'viewinv'
	)
);
}
?>
