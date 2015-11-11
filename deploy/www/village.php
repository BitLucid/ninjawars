<?php
require_once(CORE.'control/VillageController.php');

use app\Controller\VillageController;

if ($error = init(VillageController::PRIV, VillageController::ALIVE)) {
	display_error($error);
	die();
}

$command = in('command');
$controller = new VillageController();

// Switch between the different controller me/**thods.
switch(true){
	case($_SERVER['REQUEST_METHOD'] == 'POST' && self_char_id()
		&& $command=='postnow'):
		$controller->postnow();
		exit();
		break;
	case($command = 'index'):
	default:
		$command = 'index';
		$response = $controller->index();
		break;
}

// TODO: register plugin time_ago globally and call display_page function instead?
$template = prep_page($response['template'], $response['title'], $response['parts'], $response['options']);

function get_time_ago($p_params, &$tpl) {
	return time_ago($p_params['ago'], $p_params['previous_date']);
}

//$template->register_function('time_ago', 'get_time_ago');
$template->registerPlugin("function","time_ago", "get_time_ago");

$template->fullDisplay();


