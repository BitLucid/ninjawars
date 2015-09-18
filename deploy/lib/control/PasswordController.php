<?php
require_once(CORE.'control/PasswordResetRequest.php');


class PasswordController{

	/**
	 * Display the form to request a password reset link
	 **/
	public function getEmail(){
	}

	/**
	 * Send a reset link to a given user.
	**/
	public function postEmail(Request $request){
		$error = null;
		$email = $request->input('email');
		$ninja_name = $request->input('ninja_name');
		if(!$email && !$ninja_name){
			$error = 'You must specify either an email or a ninja name!';
		} else {
			$account_id = PasswordResetRequest::findAccount($email, $ninja_name);
		}

	}

	/**
	 *  Display the password reset view for the given token.
	**/
	public function getReset(string $token = null){
	}

	/**
	 * Reset the given user's password.
	**/
	public function postReset(Request $request){
		$token = $request->input('token', null);
		$new_pass = $request->input('new_password');
		$pass_confirm = $request->input('confirm_password');
		if(!$new_pass || $pass_confirm !== $new_pass){
			redirect('/password'.'?password_match_error=1');
		}
		if(!$token){
			return false;
		} else {
			PasswordResetRequest::reset($account_id, $new_pass);
			redirect('/password'.'?message=password%20reset!');
		}
	}

	/**
	 * Get the path for redirection after success/failure.
	**/
	function redirectPath(){
		return '/';
	}

}