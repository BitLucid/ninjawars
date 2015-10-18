<?php
require_once(CORE.'data/PasswordResetRequest.php');
require_once(CORE.'control/lib_crypto.php'); // For the nonce.

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;


class PasswordController{

	/**
	 * Display the form to request a password reset link
	 **/
	public function getEmail(){
		// TODO: Generates a csrf
		// Stores csrf to the cookie I guess.
		$content = render_template('request_password_reset.tpl');

		$response = new Response();
		$response->setContent($content);
		return $response;
	}

	/**
	 * Send a reset link to a given user.
	**/
	public function postEmail(Request $request){
		$error = null;
		$email = $request->get('email');
		$ninja_name = $request->get('ninja_name');
		if(!$email && !$ninja_name){
			$error = 'You must specify either an email or a ninja name!';
			$mess = null;
		} else {
			$account = PasswordResetRequest::findAccount($email, $ninja_name);
			$error = null;
			$mess = 'Email sent!';
			$this->sendEmail($account);
		}
		// TODO: Authenticate the csrf, which must match, from the session.
		return new RedirectResponse('/password'.'?'
			.($mess? 'message='.url($mess).'&' : '')
			.($error? 'error='.url($error) : '')
			);
	}

	// Send an email directly to the user with the reset instructions.
	public function sendEmail(Account $account){
		debug('tOTALLY SENDING THE EMAIL, YEAH MAN!');
		die();

	}

	/**
	 *  Display the password reset view for the given token.
	**/
	public function getReset($token = null){
		$data = PasswordResetRequest::match($token);
		$account = AccountFactory::find($data['_account_id']);

		$content = render_template('passwordreset.tpl', ['token'=>$token, 'email'=>$account->email()]);
		$response = new Response();
		$response->setContent($content);
		//$response->setStatusCode(200);
		// configure the HTTP cache headers
		$response->setMaxAge(600); // Seconds, so 10 minutes
		return $response;
	}

	/**
	 * Reset the given user's password.
	**/
	public function postReset(Request $request){
		$token = $request->get('token', null);
		$new_pass = $request->get('new_password');
		$pass_confirm = $request->get('confirm_password');
		if(!$new_pass || $pass_confirm !== $new_pass){
			return new RedirectResponse('/password'.'?password_match_error=1');
		}
		if(!$token){
			return false;
		} else {
			PasswordResetRequest::reset($account_id, $new_pass);
			return new RedirectResponse('/password'.'?message=password%20reset!');
		}
	}

	/**
	 * Get the path for redirection after success/failure.
	**/
	function redirectPath(){
		return '/';
	}

}