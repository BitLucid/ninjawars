<?php
require_once(CORE.'data/PasswordResetRequest.php');
require_once(CORE.'control/PasswordController.php');

use Symfony\Component\HttpFoundation\Request;


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

$controller = new PasswordController();

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

$page = isset($page)? $page : 'password_reset_request';
$pages = ['resetpassword'=>
	['title'=>'Reset your password', 'template'=>'resetpassword.tpl'],
	'request_password_reset'=>
	['title'=>'Request A Password Reset Email', 'template'=>'request_password_reset.tpl']
	];

display_static_page($page, $pages, $vars, $options=array());