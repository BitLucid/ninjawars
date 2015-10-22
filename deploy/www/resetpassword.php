<?php
require_once(CORE.'data/PasswordResetRequest.php');
require_once(CORE.'control/PasswordController.php');

use Symfony\Component\HttpFoundation\Request;

$command = (string) in('command');

$controller = new PasswordController();

switch (true) {
	case ($command == 'reset' && !empty($_POST)):
		$response = $controller->postReset();
	break;
	case ($command == 'reset'):
		$response = $controller->getReset();
	break;
	case ($command == 'index' && !empty($_POST)):
		$response = $controller->postEmail();
	break;
	default:
		$command == 'index';
		$response = $controller->getEmail();
	break;
}

$token = in('token');
$email = post('email');
$ninja_name = post('ninja_name');
$new_password = post('new_password');
$password_confirmation = post('password_confirmation');
$error = in('error');
$message = in('message');

$vars=['token'=>$token, 'email'=>$email, 'ninja_name'=>$ninja_name, 
	'new_password'=>$new_password, 'password_confirmation'=>$password_confirmation, 
	'error'=>$error, 'message'=>$message];



if($token !== null){ // A potentially valid reset is requested
	if($new_password === null){

		$controller->getReset($token);
		$page = 'resetpassword';
		$container = $controller->getEmailForToken($token);
		$vars['verified_email'] = $container->verified_email;
	} else {
		// finally, reset the password to new info
		$request = Request::createFromGlobals();
		$response = $controller->postReset($request);
		$response->send();
	}
} elseif(post('email') || post('ninja_name')){ // Posted request form
	$request = Request::createFromGlobals();
	$response = $controller->postEmail($request);
	$response->send();
} else{
	$response = $controller->getRequestForm(Request::createFromGlobals()); // The default, renders the initial form
	$page = 'request_password_reset';
}

display_page(
	$response->template,
	$response->title,
	$response->parts,
	isset($response->options)? $response->options : [];
);