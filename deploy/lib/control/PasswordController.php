<?php
namespace NinjaWars\core\control;

require_once(CORE.'data/PasswordResetRequest.php');
require_once(CORE.'control/lib_crypto.php'); // For the nonce.

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

use app\data\PasswordResetRequest;
use \AccountFactory;
use \Nmail;

class PasswordController{
    const PRIV           = false;
    const ALIVE          = false;
	public $debug_emails = false;


	/**
	 * Private functionality to send out the reset email.
	 * @param bool $debug_allowed Only this + DEBUG allow email debugging.
	 * @return bool
	**/
	private function sendEmail($token, $account, $debug_allowed=false){
		$email = $account->getActiveEmail();
		if(!$email){
			return false;
		}
		// Email body contents will be: Click here to reset your password: {{ url('password/reset/'.$token) }}
		$url = WEB_ROOT.'resetpassword.php?command=reset&token='.url($token);
		$rendered = render_template('email.password_reset_request.tpl', ['url'=>$url]);
		// Construct the email with Nmail, and then just send it.
		$subject='NinjaWars: Your password reset request';
		$nmail = new Nmail($email, $subject, $rendered, SUPPORT_EMAIL);
		if($debug_allowed && defined('DEBUG')) { // Then turn on debugging.
			$nmail->dump = DEBUG;
			$nmail->die_after_dump = DEBUG_ALL_ERRORS;
			$nmail->try_to_send = !DEBUG_ALL_ERRORS;
		}
		$passfail = $nmail->send();
		return $passfail;
	}

	/**
	 * Display the form to request a password reset link
	 * @param Request $request
	 * @return array
	 **/
	public function index(Request $request){
		// TODO: Generate a csrf
		$error = $request->query->get('error');
		$message = $request->query->get('message');
		$email = $request->query->get('email');
		$ninja_name = $request->query->get('ninja_name');
		
		$parts = [
			'error'=>$error, 
			'message'=>$message, 
			'email'=>$email, 
			'ninja_name'=>$ninja_name
			];
		$response = [
			'title'=>'Request a password reset', 
			'template'=>'reset.password.request.tpl', 
			'parts'=>$parts, 
			'options'=>[]
			];
		return $response;
	}

	/**
	 * Send a reset link to a given user.
	 * @param Request $request
	 * @return RedirectResponse
	**/
	public function postEmail(Request $request){
		$error = null;
		$message = null;
		$email = $request->get('email');
		$ninja_name = $request->get('ninja_name');
		if(!$email && !$ninja_name){
			$error = 'You must specify either an email or a ninja name!';
		} else {
			if($email){
				$account = AccountFactory::findByEmail($email);
			}
			if(!isset($account)){
				$account = AccountFactory::findByNinjaName($ninja_name);
			}
			if(!$account->id()){
				$error = 'Sorry, unable to find a matching account!';
			} else {
				// PWR created with default nonce
				$request = PasswordResetRequest::generate($account);
				if(empty($request)){
					throw new \RuntimeException('Password reset not created properly');
				}
				$passfail = $this->sendEmail($request->nonce, $account, $this->debug_email);
				if($passfail){
					$message = 'Your reset email was sent!';
				} else {
					$error = 'Sorry, there was a problem sending to your account!  Please contact support.';
				}
			}
		}
		// TODO: Authenticate the csrf, which must match, from the session.
		return new RedirectResponse('/resetpassword.php?'
			.($message? 'message='.url($message).'&' : '')
			.($error? 'error='.url($error) : '')
			);
	}

	/**
	 *  Obtain token, get matching request
	 * @param Request $request
	 * @return array|RedirectResponse
	**/
	public function getReset(Request $request){
		$token = $request->query->get('token');
		$req = $token? PasswordResetRequest::match($token) : null;
		if(!$req){
			$error = 'No match for your password reset found or time expired, please request again.';
			return new RedirectResponse('/resetpassword.php?command=reset&'
			.($error? 'error='.url($error) : ''));
		} else {
			$account = $req->account();
			if(!$account || !$account->getActiveEmail()){
				throw new Exception('No account found for password reset request');
			}

			$parts = [
				'token'=>$token, 
				'email'=>$account->getActiveEmail(),
				];

			$response = [
				'title'=>'Reset your password', 
				'template'=>'reset.password.tpl', 
				'parts'=>$parts, 
				'options'=>[]
				];
			// TODO: Need a way to set the max age on the response that the form will display
			return $response;
		}
	}

	/**
	 * Reset the given user's password.
	 * @param Request $request
	 * @return RedirectResponse
	**/
	public function postReset(Request $request){
		$token = $request->request->get('token', null);
		$new_password = $request->request->get('new_password');
		$password_confirmation = $request->request->get('password_confirmation');
		if($password_confirmation === null || $password_confirmation !== $new_password){
			return new RedirectResponse('/resetpassword.php?command=reset&token='.url($token).'&error='.url('Password Confirmation did not match.'));
		}
		if(!$token){
			return new RedirectResponse('/resetpassword.php?command=reset&token='.url($token).'&error='.url('No Valid Token to allow for password reset! Try again.'));
		} else {
			$req = PasswordResetRequest::match($token);
			$account = $req instanceof PasswordResetRequest? $req->account() : null;
			if($account->id()){
				if(strlen(trim($new_password)) > 3 && $new_password === $password_confirmation){
					PasswordResetRequest::reset($account, $new_password, $this->debug_emails);
					// The reset will also send an email if DEBUG_ALL_ERRORS isn't on (aka false)
					return new RedirectResponse('/resetpassword.php?message='.url('Password reset!'));
				} else {
					return new RedirectResponse('/resetpassword.php?command=reset&token='.url($token).'&error='.url('Password not long enough or does not match password confirmation!'));
				}
			} else {
				return new RedirectResponse('/resetpassword.php?command=reset&token='.url($token).'&error='.url('Token was invalid or expired! Please reset again.'));
			}
		}
	}

}
