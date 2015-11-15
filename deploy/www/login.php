<?php
require_once(CORE.'control/LoginController.php');

use app\Controller\LoginController;
use Symfony\Component\HttpFoundation\RedirectResponse;

$controller = new LoginController();

$command = in('command');

switch(true){
	case ($command=='login_request' && $_SERVER['REQUEST_METHOD'] == 'POST'):
		//debug($_POST);
		//die();
		$response = $controller->requestLogin();
	break;
	default:
		$response = $controller->index();
	break;
}

if($response instanceof RedirectResponse){
	$response->send();
} else {
	display_page(
		$response['template'],
		$response['title'],
		$response['parts'],
		$response['options']
	);
}
