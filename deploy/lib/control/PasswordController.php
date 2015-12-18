<?php
require_once(CORE.'data/PasswordResetRequest.php');
require_once(CORE.'control/lib_crypto.php'); // For the nonce.

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

use app\data\PasswordResetRequest;

class PasswordResponse{
	public $title;
	public $template;
	public $parts;
	public $options;
}

class PasswordController{
	public $debug_emails = true;


	/**
	 * Send out the password reset email to a requested account's email.
	**/
	private function sendEmail($token, $account, $debug_allowed=true){
		$email = $account->getActiveEmail();
		if(!$email){
			return false;
		}
		// Email body contents will be: Click here to reset your password: {{ url('password/reset/'.$token) }}
		$url = WEB_ROOT.'resetpassword.php?command=reset&token='.url($token);
		$rendered = render_template('email.password_reset_request.tpl', ['url'=>$url]);
		// Construct the email with Nmail, and then just send it.
		$nmail = new Nmail($email, $subject='NinjaWars: Your password reset request', $rendered, SUPPORT_EMAIL);
		if($debug_allowed && defined('DEBUG')) {
			$nmail->dump = DEBUG;
			$nmail->die_after_dump = DEBUG_ALL_ERRORS;
			$nmail->try_to_send = !DEBUG_ALL_ERRORS;
		}
		$passfail = $nmail->send();
		return $passfail;
	}

	/**
	 * Get the email associated with a token.
	**/
	private function getEmailForToken($token){
		$request = PasswordResetRequest::match($token);
		$email = isset($request->email)? $data['email'] : null;
		$container = new stdClass;
		$container->verified_email = $email;
		return $container;
	}

	/**
	 * Display the form to request a password reset link
	 * @param Request $request
	 **/
	public function getEmail(Request $request){
		// TODO: Generate a csrf
		$error = $request->query->get('error');
		$message = $request->query->get('message');
		$email = $request->query->get('email');
		$ninja_name = $request->query->get('ninja_name');
		
		$parts = ['error'=>$error, 'message'=>$message, 'email'=>$email, 'ninja_name'=>$ninja_name];
		$response = ['title'=>'Request a password reset', 'template'=>'request_password_reset.tpl', 'parts'=>$parts, 'options'=>[]];
		return $response;
	}

	/**
	 * Send a reset link to a given user.
	 * @param Request $request
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
			if($email){
				$account = AccountFactory::findByEmail($email);
			}
			if(!$account){
				$account = AccountFactory::findByNinjaName($ninja_name);
			}
			if(!$account->id()){
				$error = 'Sorry, unable to find a matching account!';
			} else {
				// Nonce will be created automatically.
				$request = PasswordResetRequest::generate($account);
				$passfail = $this->sendEmail($request->nonce, $account);
				if($passfail){
					$error = null;
					$mess = 'Your reset email was sent!';
				} else {
					$error = 'Sorry, there was a problem sending to your account!  Please contact support.';
				}
			}
		}
		// TODO: Authenticate the csrf, which must match, from the session.
		return new RedirectResponse('/resetpassword.php?'
			.($mess? 'message='.url($mess).'&' : '')
			.($error? 'error='.url($error) : '')
			);
	}

	/**
	 *  Display the password reset view for the given token.
	 * @param Request $request
	**/
	public function getReset(Request $request){
		$token = $request->request->get('token');
		$data = PasswordResetRequest::match($token);
		if(!$token || empty($data)){
			$error = 'No match for your password reset found or time expired, please request again.';
			return new RedirectResponse('/resetpassword.php?command=reset&'
			.($error? 'error='.url($error) : ''));
		}
		$account = AccountFactory::find($data['account_id']);

		$parts = [
			'token'=>$token, 
			'email'=>$account->getActiveEmail()
			];

		$reponse = [
			'title'=>'Reset your password', 
			'template'=>'resetpassword.tpl', 
			'parts'=>$parts, 
			'options'=>[]
			];
		// TODO: Need a way to set the max age on the response that the form will display
		return $response;
	}

	/**
	 * Reset the given user's password.
	 * @param Request $request
	**/
	public function postReset(Request $request){
		$token = $request->request->get('token', null);
		$new_password = $request->request->get('new_password');
		$password_confirmation = $request->request->get('password_confirmation');
		if($password_confirmation === null || $password_confirmation !== $new_password){
			return new RedirectResponse('/resetpassword.php?command=reset&token='.url($token).'&error='.url('Password Confirm did not match'));
		}
		if(!$token){
			return new RedirectResponse('/resetpassword.php?command=reset&token='.url($token).'&error='.url('No Valid Token to allow for password reset! Try again.'));
		} else {
			$data = PasswordResetRequest::match($token);
			$account_id = isset($data['account_id'])? $data['account_id'] : null;
			if(!empty($data) && $account_id){
				if(strlen(trim($new_password)) > 3 && $new_password === $password_confirmation){
					PasswordResetRequest::reset(new Account($account_id), $new_password, $this->debug_emails);
					// The reset will also send an email if DEBUG_ALL_ERRORS isn't on (aka false)
					return new RedirectResponse('/resetpassword.php?message='.url('Password reset!'));
				} else {
					return new RedirectResponse('/resetpassword.php?command=reset&token='.url($token).'&error='.url('Password not long enough or does not match password confirm!'));
				}
			} else {
				return new RedirectResponse('/resetpassword.php?command=reset&token='.url($token).'&error='.url('Token was invalid or expired! Please reset again.'));
			}
		}
	}

}
