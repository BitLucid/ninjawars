<?php
require_once(LIB_ROOT.'control/lib_inventory.php');
require_once(CORE.'control/WorkController.php');

use app\Controller\WorkController;

if ($error = init(WorkController::PRIV, WorkController::ALIVE)) {
	display_error($error);
	die();
}

$command = in('command');

$controller = new WorkController();

// Switch between the different controller methods.
switch(true){
	case($_SERVER['REQUEST_METHOD'] == 'POST' && $command=='request_work'):
		$response = $controller->requestWork();
	break;
	case($command = 'index'):
	default:
		$command = 'index';
		$response = $controller->index();
	break;
}

// Display the page with the template, title or header vars, template parts, and page options
display_page($response['template'], $response['title'], $response['parts'], $response['options']);
