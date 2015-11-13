<?php
namespace app\Controller;

use SESSION;

require_once(LIB_ROOT.'control/lib_player.php'); // Player info display pieces.
require_once(LIB_ROOT.'control/lib_status.php'); // Status alterations.

// TODO: Lock account info behind password or password reset choice wall?
// TODO: Current account's characters list.
// TODO: Create another character interface...
// TODO: Choose next active character interface...

/**
 * Handle updates for changing account password, changing account email and showing the account page
 */
class AccountController {

	const ALIVE                  = false;
	const PRIV                   = true;

	/**
	 * Show the change email form
	 */
	public function showChangeEmailForm()
	{
		// explicitly define command value ?
		$command = in('command');

		$parts = [
			'command' => $command,
		];

		return $this->render($parts);
	}

	/**
	* Change account email and validate authenticity
	*/
	public function changeEmail(){
		// confirm_delete
		$self_info 	= self_info();
		$user_id  	= self_char_id();
		$passW 		= in('passw', null);
		$username 	= $self_info['uname'];

		$in_newEmail     = trim(in('newemail'));
		$in_confirmEmail = trim(in('confirmemail'));

		$error = '';
		$successMessage = '';

		$verify = is_authentic($username, $passW);

		if ($verify) {
			if ($in_newEmail === $in_confirmEmail) {
				if (!email_is_duplicate($in_newEmail)) {
					if (email_fits_pattern($in_newEmail)) {
						changeEmail($user_id, $in_newEmail);
						$successMessage = 'Your email has been updated.';
					} else {
						$error = 'Your email must be a valid email address containing a domain name and no spaces.';
					}
				} else {
					$error = 'The email you provided is already in use.';
				}
			} else {
				$error = 'Your new emails did not match.';
			}
		} else {
			$error = 'You did not provide the correct current password.';
		}

		$parts = [
			'error' => $error,
			'successMessage' => $successMessage,
		];

		return $this->render($parts);
	}

	/**
	 * Show Change Password form
	 */
	public function showChangePasswordForm()
	{
		// explicitly define command value ?
		$command = in('command');

		$parts = [
			'command' => $command,
		];

		return $this->render($parts);
	}

	/**
	 * Change account password
	 */
	public function changePassword(){
		$self_info 	= self_info();
		$user_id  	= self_char_id();
		$passW 		= in('passw', null);
		$username 	= $self_info['uname'];

		$in_newPass     = trim(in('newpassw'));
		$in_confirmPass = trim(in('confirmpassw'));

		$error = '';
		$successMessage = '';

		$verify = is_authentic($username, $passW);

		if ($verify) {
			if ($in_newPass === $in_confirmPass) {
				changePassword($user_id, $in_newPass);
				$successMessage = 'Your password has been updated.';
			} else {
				$error = 'Your new passwords did not match.';
			}
		} else {
			$error = 'You did not provide the correct current password.';
		}

		$parts = [
			'error' => $error,
			'successMessage' => $successMessage,
		];

		return $this->render($parts);
	}

	/**
	 * Show delete account confirmation form
	 */
	public function deleteAccountConfirmation()
	{
		// explicitly define command value ?
		$command = in('command');

		$parts = [
			'command' => $command,
		];

		return $this->render($parts);
	}

	/**
	* Make account non-operational
	*/
	public function deleteAccount(){
		$self_info 	= self_info();
		$user_id  	= self_char_id();
		$passW 		= in('passw', null);
		$username 	= $self_info['uname'];

		$error 		= '';
		$command 	= in('command');
		$delete_attempts = SESSION::is_set('delete_attempts') ? SESSION::get('delete_attempts') : 0;

		$verify = is_authentic($username, $passW);

		if ($verify && empty($delete_attempts)) {
			// only allow account deletion on first attempt
			pauseAccount($user_id); // This may redirect and stuff?
			logout_user();
		} else {
			SESSION::set('delete_attempts', $delete_attempts+1);
			$error = 'Deleting of account failed, please email '.SUPPORT_EMAIL;
		}

		$parts = [
			'command' => $command,
			'error' => $error,

			'delete_attempts' => $delete_attempts,
		];

		return $this->render($parts);
	}

	/**
	* Display the default account page
	**/
	public function index() {
		return $this->render([]);
	}

	private function render($parts) {
		// default parts
		$account_info = self_account_info();

		// Get the existing oauth info, if any.
		$oauth_provider = $account_info['oauth_provider'];
		$oauth = $oauth_provider && $account_info['oauth_id'];

		$player           = self_info();
		$gravatar_url     = generate_gravatar_url($player['player_id']);

		$parts = array_merge([
			'gravatar_url' => $gravatar_url,
			'player' => $player,
			'account_info' => $account_info,
			'oauth_provider' => $oauth_provider,
			'oauth' => $oauth,

			'successMessage' => false,
			'error' => false,

			'command' => '',
			'delete_attempts' => 0,
		], $parts);

		return [
			'template' => 'account.tpl',
			'title'    => 'Your Account',
			'parts'    => $parts,
			'options'  => [
				'quickstat' => 'player',
			],
		];
	}
}
