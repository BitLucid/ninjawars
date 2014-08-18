<?php
$login_requested = (bool) in('login_request'); // Login was requested.
$logged_out          = in('logged_out'); // Logout page redirected to this one, so display the message.
$login_error_message = in('error'); // Error to display after unsuccessful login and redirection.

$stored_username = isset($_COOKIE['username'])? $_COOKIE['username'] : null;
$referrer        = (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null);

$is_logged_in = is_logged_in();
if($is_logged_in){
	redirect('/');
}

init($private=false, $alive=false);

$pass = in('pass');
$username_requested = in('user');

if(!$login_error_message){
	// $login_settings must stay in sync with arguments to perform_login_if_requested
	$login_settings = array($logged_out, $username_requested, $pass, $referrer, $stored_username);
	// Create the vars for the template directly from the array the function returns.
	$vars = perform_login_if_requested($is_logged_in, $login_requested, $login_settings);
}


$page = 'login';
$pages = array('login'=>array('title'=>'Login', 'template'=>'login.tpl'));

display_static_page($page, $pages, 
	$vars=array(
		'is_logged_in'=>$is_logged_in, 
		'login_error_message'=>$login_error_message, 
		'logged_out'=>$logged_out, 
		'referrer'=>$referrer, 
		'stored_username'=>$stored_username
		)
	);
