<?php
require_once(CORE.'data/PasswordResetRequest.php');
require_once(CORE.'control/PasswordController.php');

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

$command = (string) in('command');

$controller = new PasswordController();

$request = Request::createFromGlobals();
$method = isset($_SERVER['REQUEST_METHOD'])? $_SERVER['REQUEST_METHOD'] : null;

switch (true) {
	case ($command == 'reset' && $method === 'POST'):
		$response = $controller->postReset($request);
	break;
	case ($command == 'reset'):
		$response = $controller->getReset($request);
	break;
	case ($command == 'email' && $method === 'POST'):
		$response = $controller->postEmail($request);
	break;
	default:
		$command == 'index';
		$response = $controller->getEmail($request);
	break;
}

if($response instanceof RedirectResponse){
	$reponse->send();
} else {
	display_page(
		$response['template'],
		$response['title'],
		$response['parts'],
		$response['options']
	);
}