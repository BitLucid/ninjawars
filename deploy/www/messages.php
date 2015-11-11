<?php
require_once(CORE.'control/MessagesController.php');

use Symfony\Component\HttpFoundation\RedirectResponse;
use app\Controller\MessagesController;

$private    = true;
$alive      = false;

if ($error = init($private, $alive)) {
	display_error($error);
} else {

	$command = in('command');

	$controller = new MessagesController();

	switch(true){
		case($command == 'clan' && $_SERVER['REQUEST_METHOD'] == 'POST'):
			$response = $controller->sendClan();
		break;
		case($command == 'clan'):
			$response = $controller->viewClan();
		break;
		case($command == 'personal' && $_SERVER['REQUEST_METHOD'] == 'POST'):
			$response = $controller->sendPersonal();
		break;
		case($command == 'delete_clan' && $_SERVER['REQUEST_METHOD'] == 'POST'):
			$response = $controller->deleteClan(); // Just delete clan messages.
		break;
		case($command == 'delete_messages' && $_SERVER['REQUEST_METHOD'] == 'POST'):
			$response = $controller->deletePersonal(); // Just delete existing personal messages.
		break;
		default:
			$command = 'personal';
			$response = $controller->viewPersonal();
	}

	if($response instanceof RedirectResponse){
		$response->send();
	} else {
		display_page(
			  $response['template']
			, $response['title']
			, $response['parts']
			, ['quickstat' => false]
		);
	}

}

