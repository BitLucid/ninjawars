<?php
require_once(LIB_ROOT."control/lib_player_list.php");
require_once(LIB_ROOT."control/lib_grouping.php");
require_once(LIB_ROOT."data/lib_npc.php");
require_once(CORE.'control/ConsiderController.php');

$private    = false;
$alive      = false;

use Symfony\Component\HttpFoundation\RedirectResponse;
use app\combat\ConsiderController;





if ($error = init($private, $alive)) {
	header('Location: list.php');
} else {


	$controller = new ConsiderController();

	$command = in('command');

	switch(true){
		case($command== 'search'):
			$response = $controller->search(); // For enemies to store
		break;
		case($_SERVER['REQUEST_METHOD'] == 'POST' && $command== 'add'):
			$response = $controller->addEnemy(); // Add an enemy
		break;
		case($_SERVER['REQUEST_METHOD'] == 'POST' && $command== 'delete'):
			$response = $controller->deleteEnemy(); // Delete someone from enemy list
		break;
		case($command == 'index'):
		default:
			// Display the various things to consider fighting
			$response = $controller->index();
		break;
	}

	if($response instanceof RedirectResponse){
		$response->send();
	} else {
		display_page(
			  $response['template']
			, $response['title'] // *** Page Title or head info***
			, $response['parts']
			, $response['options']
		);
	}
}
