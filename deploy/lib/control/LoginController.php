<?php
namespace app\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use \RunTimeException;
use \Constants;

class LoginController{

	const ALIVE                  = false;
	const PRIV                   = false;
	
	public function __construct(){
	}

	/**
	 * Try to perform a login, perform_login_if_requested will redirect as necessary
	 */
	public function requestLogin() {
		$logged_out          = in('logged_out'); // Logout page redirected to this one, so display the message.
		$login_error_message = in('error'); // Error to display after unsuccessful login and redirection.

		$is_logged_in = is_logged_in();

		$pass = post('pass');
		$username_requested = post('user');

		if ($logged_out) {
			logout_user(); // Perform logout if requested!
		} else if ($username_requested === null || $pass === null) {
			$login_error_message = 'No username or no password specified';
		}

		if (!$login_error_message && !$is_logged_in) {
			$login_error_message = perform_login_if_requested($username_requested, $pass);
		}

		if ($login_error_message) {
			return new RedirectResponse('/login.php?error='.url($login_error_message));
		} else { // Successful login, go to the main page
			return new RedirectResponse('/');
		}
	}

	/**
	 * Display standard login page.
	**/
	public function index(){
		$logged_out          = in('logged_out'); // Logout page redirected to this one, so display the message.
		$login_error_message = in('error'); // Error to display after unsuccessful login and redirection.

		$stored_username = isset($_COOKIE['username'])? $_COOKIE['username'] : null;
		$referrer        = (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null);

		$is_logged_in = is_logged_in();
		if($is_logged_in){
			return new RedirectResponse(WEB_ROOT);
		}

		$pass = in('pass');
		$username_requested = in('user');
		$parts = [
			'is_logged_in'=>$is_logged_in, 
			'login_error_message'=>$login_error_message, 
			'logged_out'=>$logged_out, 
			'referrer'=>$referrer, 
			'stored_username'=>$stored_username,
			];
		return $this->render('Login', $parts);
	}

	/**
	 * Render the concrete elements of the response
	**/
	public function render($title, $parts){
		$response = ['template'=>'login.tpl',
					 'title'=>$title,
					 'parts'=>$parts,
					 'options'=>[]];
		return $response;
	}
}
