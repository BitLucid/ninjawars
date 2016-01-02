<?php
require_once(LIB_ROOT."control/lib_inventory.php");
require_once(CORE."control/InventoryController.php");

use app\Controller\InventoryController;

$private   = true;
$alive     = false;

if ($error = init($private, $alive)) {
	display_error($error);
} else {

	$controller = new InventoryController;
	$response = $controller->index();

	display_page($response['template'], $response['title'], $response['parts'], $response['options']);
}
