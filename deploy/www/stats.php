<?php
require_once(CORE.'control/StatsController.php'); // Player info display pieces.

use app\Controller\StatsController;

if ($error = init(StatsController::PRIV, StatsController::ALIVE)) {
	display_error($error);
	die();
}

$controller = new StatsController;
$command = in('command');

// Switch between the different controller methods.
switch($command){
	case 'change_details':
		$controller->changeDetails();
		exit();
		break;
	case 'update_profile':
		$controller->updateProfile();
		exit();
		break;
	case 'index':
	default:
		$command = 'index';
		$response = $controller->index();
		break;
}

// Display the page with the template, title or header vars, template parts, and page options
display_page($response['template'], $response['title'], $response['parts'], $response['options']);
