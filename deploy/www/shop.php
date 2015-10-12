<?php
require_once(LIB_ROOT."control/lib_inventory.php");
require_once(LIB_ROOT."control/ShopController.php");

use app\Controller\ShopController;

if ($error = init(ShopController::$private, ShopController::$alive)) {
	display_error($error);
} else {
	$shop = new ShopController();

	if ($_SERVER['REQUEST_METHOD'] === 'POST' && in('purchase') == '1') {
		$shop->buy();
	} else {
		$shop->index();
	}
} // End of no display error.


