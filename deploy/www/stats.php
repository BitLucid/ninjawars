<?php
require_once(CORE.'control/StatsController.php'); // Player info display pieces.

use Symfony\Component\HttpFoundation\RedirectResponse;
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
		$response = $controller->changeDetails();
		break;
	case 'update_profile':
		$response = $controller->updateProfile();
		break;
	case 'index':
	default:
		$command = 'index';
		$response = $controller->index();
		break;
}

if ($response instanceof RedirectResponse) {
	$response->send();
} else {
	// Display the page with the template, title or header vars, template parts, and page options
	display_page($response['template'], $response['title'], $response['parts'], $response['options']);
}