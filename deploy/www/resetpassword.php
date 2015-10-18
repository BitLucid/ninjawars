<?php
require_once(CORE.'data/PasswordResetRequest.php');
require_once(CORE.'control/PasswordController.php');

init($private=false, $alive=false);

$token = in('token');

$controller = new PasswordController();

if($token){
	$controller->getReset($token);
} elseif(post('email') || post('ninja_name')){
	$request = Request::createFromGlobals();
	$result = $controller->postEmail($request);
} else{
	$controller->getEmail(); // The default, displays the form
}
if(!$token && !post('email')

$page = 'resetpassword';
$pages = ['resetpassword'=>
	['title'=>'Request A Password Reset Email', 'template'=>'resetpassword.tpl']];
$vars=[];

display_static_page($page, $pages, $vars, $options=array());