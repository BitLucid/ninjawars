<?php
$login_request = in('login_request');
$login           = !empty($login_request); // A request to login.
$logged_out          = in('logged_out'); // Logout page redirected to this one, so display the message.
$login_error = in('error', null); // Error to display after unsuccessful login and redirection.

$stored_username = isset($_COOKIE['username'])? $_COOKIE['username'] : null;
$referrer        = (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null);

$is_logged_in = self_char_id();

init($private=false, $alive=false);




// already logged in/login behaviors
if ($logged_out){
	logout_user();
	$is_logged_in = false;
} elseif (!$is_logged_in) { // Perform login if they aren't already logged in.
	if ($login) { 	// Request to login was made.
	
		$username_requested = in('user');

		$login_attempt_info = array('username'=>$username_requested, 'user_agent'=>$_SERVER['HTTP_USER_AGENT'], 'ip'=>$_SERVER['REMOTE_ADDR'], 'successful'=>0, 'additional_info'=>$_SERVER);
	
		$logged_in    = login_user($username_requested, in('pass'));
		$is_logged_in = $logged_in['success'];

		if (!$is_logged_in) { // Login was attempted, but failed, so display an error.
			store_auth_attempt($login_attempt_info);
			$login_error = $logged_in['login_error'];
			redirect("login.php?error=".urlencode($login_error));
		} else {
			// log a successful login attempt
			$login_attempt_info['successful']=1;
			store_auth_attempt($login_attempt_info);
		
			// Successful login, go to the main page...
			redirect("index.php");
		}
	}
}


$page = 'login';
$pages = array('login'=>array('title'=>'Login', 'template'=>'login.tpl'));

display_static_page($page, $pages, $vars=array('is_logged_in'=>$is_logged_in, 'login_error'=>$login_error, 'logged_out'=>$logged_out, 'referrer'=>$referrer, 'stored_username'=>$stored_username), $options=array());
?>
