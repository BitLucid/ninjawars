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
	public function getRequestForm(){
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
		$mess = null;
		$email = $request->get('email');
		$ninja_name = $request->get('ninja_name');
		if(!$email && !$ninja_name){
			$error = 'You must specify either an email or a ninja name!';
			$mess = null;
		} else {
			$account = PasswordResetRequest::findAccount($email, $ninja_name);
			if(!$account){
				$error = 'Unable to find a matching account!';
			} else {
				$token = PasswordResetRequest::request($account->getId()); // Nonce will be created automatically.
				$passfail = $this->sendEmail($token, $account);
				if($passfail){
					$error = null;
					$mess = 'Reset Email sent!';
				} else {
					$error = 'Sorry, there was a problem sending to your account!  Please contact support.';
				}
			}
		}
		// TODO: Authenticate the csrf, which must match, from the session.
		return new RedirectResponse('/resetpassword.php'.'?'
			.($mess? 'message='.url($mess).'&' : '')
			.($error? 'error='.url($error) : '')
			);
	}

	// Get the email associated with a token.
	public function getEmailForToken($token){
		$data = PasswordResetRequest::match($token);
		$email = isset($data['email'])? $data['email'] : null;
		$container = new stdClass;
		$container->verified_email = $email;
		return $container;
	}

	// Send an email directly to the user with the reset instructions.
	public function sendEmail($token, Account $account){
		$passfail = PasswordResetRequest::send($token, $account->getActiveEmail());
		return $passfail;
	}

	/**
	 *  Display the password reset view for the given token.
	**/
	public function getReset($token){
		$data = PasswordResetRequest::match($token);
		if(empty($data) || !$token){
			$error = 'No match for your password reset found or time expired, please request again.';
			return new RedirectResponse('/resetpassword.php'.'?'
			.($error? 'error='.url($error) : ''));
		}
		$account = AccountFactory::find($data['account_id']);

		$content = render_template('resetpassword.tpl', ['token'=>$token, 'email'=>$account->getActiveEmail()]);
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
		$token = $request->request->get('token', null);
		$new_password = $request->request->get('new_password');
		$password_confirmation = $request->request->get('password_confirmation');
		if($password_confirmation === null || $password_confirmation !== $new_password){
			return new RedirectResponse('/resetpassword.php'.'?token='.url($token).'&error='.url('Password Confirm did not match'));
		}
		if(!$token){
			return new RedirectResponse('/resetpassword.php'.'?token='.url($token).'&error='.url('No Valid Token to allow for password reset! Try again.'));
		} else {
			$data = PasswordResetRequest::match($token);
			$account_id = isset($data['account_id'])? $data['account_id'] : null;
			if(!empty($data) && $account_id){
				if(strlen(trim($new_password)) > 3 && $new_password === $password_confirmation){
					PasswordResetRequest::reset(new Account($account_id), $new_password); // Will also send email.
					return new RedirectResponse('/resetpassword.php'.'?message='.url('Password reset!'));
				} else {
					return new RedirectResponse('/resetpassword.php'.'?token='.url($token).'&error='.url('Password not long enough or does not match password confirm!'));
				}
			} else {
				return new RedirectResponse('/resetpassword.php'.'?token='.url($token).'&error='.url('Token was invalid or expired! Please reset again.'));
			}
		}
	}

	/**
	 * Get the path for redirection after success/failure.
	**/
	function redirectPath(){
		return '/';
	}

}