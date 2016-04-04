<?php
namespace NinjaWars\core\control;

use NinjaWars\core\data\DatabaseConnection;
use NinjaWars\core\data\Player;
use NinjaWars\core\data\Account;
use NinjaWars\core\extensions\SessionFactory;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Handle updates for changing account password, changing account email and showing the account page
 */
class AccountController {
    const ALIVE = false;
    const PRIV  = true;

    /**
     * Show the change email form
     */
    public function showChangeEmailForm() {
        $command = 'show_change_email_form';

        $parts = [
            'command' => $command,
        ];

        return $this->render($parts);
    }

    /**
     * Change account email and validate authenticity
     */
    public function changeEmail() {
        // confirm_delete
        $player     = Player::find(SessionFactory::getSession()->get('player_id'));
        $self_info 	= $player->dataWithClan();
        $passW 		= in('passw', null);
        $username 	= $self_info['uname'];

        $in_newEmail     = trim(in('newemail'));
        $in_confirmEmail = trim(in('confirmemail'));

        $error = '';
        $successMessage = '';

        $verify = self::is_authentic($username, $passW);

        if ($verify) {
            if ($in_newEmail === $in_confirmEmail) {
                $account = Account::findByEmail($in_newEmail);

                if ($account !== null) {
                    try {
                        $account = Account::findByChar($player);
                        $account->setActiveEmail($in_newEmail);
                        $account->save();

                        $successMessage = 'Your email has been updated.';
                    } catch (\InvalidArgumentException $e) {
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
    public function showChangePasswordForm() {
        // explicitly define command value ?
        $command = 'show_change_password_form';

        $parts = [
            'command' => $command,
        ];

        return $this->render($parts);
    }

    /**
     * Change account password
     */
    public function changePassword() {
        $player     = Player::find(SessionFactory::getSession()->get('player_id'));
        $self_info 	= $player->dataWithClan();
        $passW 		= in('passw', null);
        $username 	= $self_info['uname'];

        $in_newPass     = trim(in('newpassw'));
        $in_confirmPass = trim(in('confirmpassw'));

        $error = '';
        $successMessage = '';

        $verify = self::is_authentic($username, $passW);

        if ($verify) {
            if ($in_newPass === $in_confirmPass) {
                $this->_changePassword($player->id(), $in_newPass);
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
     */
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
    public function deleteAccountConfirmation() {
        $session    = SessionFactory::getSession();
        $command = 'show_confirm_delete_form';
        $delete_attempts = $session->get('delete_attempts', 0);

        $parts = [
            'command' => $command,
            'delete_attempts' => $delete_attempts,
        ];

        return $this->render($parts);
    }

    /**
     * Make account non-operational
     */
    public function deleteAccount() {
        $session    = SessionFactory::getSession();
        $player     = Player::find(SessionFactory::getSession()->get('player_id'));
        $self_info 	= $player->dataWithClan();
        $passW 		= in('passw', null);
        $username 	= $self_info['uname'];

        $error 		= '';
        $command 	= in('command');
        $delete_attempts = $session->get('delete_attempts', 0);

        $verify = self::is_authentic($username, $passW);

        if ($verify && empty($delete_attempts)) {
            // only allow account deletion on first attempt
            $player = Player::find($player->id());
            $player->active = 0;
            $player->save();

            $account = Account::findByChar($player);
            $account->setOperational(false);
            $account->save();

            logout_user(); // Wipe session & logout the user
            return new RedirectResponse('/logout/loggedout');
        } else {
            $session->set('delete_attempts', $delete_attempts+1);
            $error = 'Deleting of account failed, please email '.SUPPORT_EMAIL;
        }

        $parts = [
            'command'         => $command,
            'error'           => $error,
            'delete_attempts' => $delete_attempts,
        ];

        return $this->render($parts);
    }

    /**
     * Display the default account page
     */
    public function index() {
        return $this->render([]);
    }

    /**
     */
    private function render($p_parts) {
        $account = Account::findById(SessionFactory::getSession()->get('account_id'));
        $player  = Player::find(SessionFactory::getSession()->get('player_id'));

        $parts = [
            'gravatar_url'    => $player->avatarUrl(),
            'player'          => $player->dataWithClan(),
            'account'         => $account,
            'oauth_provider'  => ($account ? $account->oauth_provider : ''),
            'oauth'           => ($account ? $account->oauth_provider && $account->oauth_id : ''),
            'successMessage'  => false,
            'error'           => false,
            'command'         => '',
            'delete_attempts' => 0,
        ];

        return [
            'template' => 'account.tpl',
            'title'    => 'Your Account',
            'parts'    => array_merge($parts, $p_parts),
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
