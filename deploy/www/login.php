<?php
$login           = !empty($action); // A request to login.
$logged_out          = in('logged_out'); // Logout page redirected to this one, so display the message.
$login_error     = false;

$stored_username = isset($_COOKIE['username'])? $_COOKIE['username'] : null;
$referrer        = (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null);

$is_logged_in    = is_logged_in();

// Eventually this page will simply be rerouted through apache to the static page system.

init($private=false, $alive=false);


// already logged in/login behaviors
if ($is_logged_in) {   // When user is already logged in.
	$logged_in['success'] = $is_logged_in; 
} else { // Only honor a request to login if they aren't already.
	if ($login) { 	// Request to login was made.
		$logged_in    = login_user(in('user', null), in('pass'));
		$is_logged_in = $logged_in['success'];

		if (!$is_logged_in) { // Login was attempted, but failed, so display an error.
			$login_error = true;
		} else {
			header("Location: login.php"); 
			exit(); // Login redirect to prevent the refresh postback problem.
		}
	}
}


$page = 'login';
$pages = array('login'=>array('title'=>'Login', 'template'=>'login.tpl'));

display_static_page($page, $pages, $vars=array('is_logged_in'=>$is_logged_in, 'login_error'=>$login_error, 'logged_out'=>$logged_out, 'referrer'=>$referrer, 'stored_username'=>$stored_username), $options=array());








?>
