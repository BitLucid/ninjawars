<?php
namespace NinjaWars\core\control;

use NinjaWars\core\data\DatabaseConnection;
use NinjaWars\core\control\SessionFactory;

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
						$this->_changeEmail($user_id, $in_newEmail);
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
	 * Internal method to update account_identity in the database.
	 * @todo Make this done by the model
	**/
	private function _changeEmail($p_playerID, $p_newEmail) {
		$changeEmailQuery1 = "UPDATE accounts SET account_identity = :identity, active_email = :email WHERE account_id = (SELECT _account_id FROM account_players WHERE _player_id = :pid)";

		$statement = DatabaseConnection::$pdo->prepare($changeEmailQuery1);
		$statement->bindValue(':pid', $p_playerID);
		$statement->bindValue(':identity', $p_newEmail);
		$statement->bindValue(':email', strtolower($p_newEmail));
		$statement->execute();
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
				$this->_changePassword($user_id, $in_newPass);
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
	 * Internal method to update an account crypt hash in the database
	 * @todo Maybe make functionality in the model to have this done.
	**/
	private function _changePassword($p_playerID, $p_newPassword) {
		$changePasswordQuery = "UPDATE accounts SET phash = crypt(:password, gen_salt('bf', 8)) WHERE account_id = (SELECT _account_id FROM account_players WHERE _player_id = :pid)";

		$statement = DatabaseConnection::$pdo->prepare($changePasswordQuery);
		$statement->bindValue(':pid', $p_playerID);
		$statement->bindValue(':password', $p_newPassword);
		$statement->execute();
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
		$session    = SessionFactory::getSession();
		$self_info 	= self_info();
		$user_id  	= self_char_id();
		$passW 		= in('passw', null);
		$username 	= $self_info['uname'];

		$error 		= '';
		$command 	= in('command');
		$delete_attempts = $session->get('delete_attempts', 0);

		$verify = self::is_authentic($username, $passW);

		if ($verify && empty($delete_attempts)) {
			// only allow account deletion on first attempt
			$this->pauseAccount($user_id); // This may redirect and stuff?
			logout_user();
		} else {
			$session->set('delete_attempts', $delete_attempts+1);
			$error = 'Deleting of account failed, please email '.SUPPORT_EMAIL;
		}

		$parts = [
			'command' => $command,
			'error' => $error,

			'delete_attempts' => $delete_attempts,
		];

		return $this->render($parts);
	}

	// Make a whole account non-operational, unable to login, and not active.
	public function pauseAccount($p_playerID) {
		$accountActiveQuery = 'UPDATE accounts SET operational = false WHERE account_id = (SELECT _account_id FROM account_players WHERE _player_id = :pid)';
		$playerConfirmedQuery = 'UPDATE players SET active = 0 WHERE player_id = :pid';

		$statement = DatabaseConnection::$pdo->prepare($playerConfirmedQuery);
		$statement->bindValue(':pid', $p_playerID);
		$statement->execute();

		$statement = DatabaseConnection::$pdo->prepare($accountActiveQuery);
		$statement->bindValue(':pid', $p_playerID);
		$statement->execute();
		$count = $statement->rowCount();

		return ($count > 0);
	}

	/**
	 * Display the default account page
	 **/
	public function index() {
		return $this->render([]);
	}

	private function render($parts) {
		// default parts
		$account_info = account_info(account_id());

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

	/**
	 * Just do a check whether the input username and password is valid
	 *
	 * @return boolean
	 */
	public static function is_authentic($p_user, $p_pass) {
		$data = authenticate($p_user, $p_pass, false);

		return (isset($data['authenticated']) && (bool)$data['authenticated']);
	}

}
