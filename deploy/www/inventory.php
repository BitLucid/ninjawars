<?php
$private   = true;
$alive     = false;

if ($error = init($private, $alive)) {
	display_error($error);
} else {

$user_id = get_user_id();
DatabaseConnection::getInstance();
$statement = DatabaseConnection::$pdo->prepare("SELECT amount AS c, item FROM inventory WHERE owner = :owner GROUP BY item, amount");
$statement->bindValue(':owner', $user_id);
$statement->execute();

if ($data = $statement->fetch()) {
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

	do {
		$items[$data['item']] = $data['c'];
	} while ($data = $statement->fetch());
} else {
	$items = false;
}

display_page(
	'inventory.tpl'
	, 'Your Inventory'
	, array(
		'gold'        => getGold($username)
		, 'items'     => $items
		, 'item_data' => $item_data
		, 'username'  => $username
	)
	, array(
		'quickstat' => 'viewinv'
		, 'alive'   => $alive
		, 'private' => $private
	)
);
}
?>
