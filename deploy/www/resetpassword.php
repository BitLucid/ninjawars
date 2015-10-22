<?php
require_once(CORE.'data/PasswordResetRequest.php');
require_once(CORE.'control/PasswordController.php');

use Symfony\Component\HttpFoundation\Request;

$command = (string) in('command');

$controller = new PasswordController();

switch (true) {
	case ($command == 'reset' && !empty($_POST)):
		$response = $controller->postReset(Request::createFromGlobals());
	break;
	case ($command == 'reset'):
		$response = $controller->getReset(Request::createFromGlobals());
	break;
	case ($command == 'index' && !empty($_POST)):
		$response = $controller->postEmail(Request::createFromGlobals());
	break;
	default:
		$command == 'index';
		$response = $controller->getEmail(Request::createFromGlobals());
	break;
}

if($response instanceof RedirectResponse){
	$reponse->send();
} else {
	display_page(
		$response['template'],
		$response['title'],
		$response['parts'],
		isset($response['options'])? $response['options'] : [];
	);
}