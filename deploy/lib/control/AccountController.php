<?php
namespace NinjaWars\core\control;

use NinjaWars\core\control\AbstractController;
use NinjaWars\core\data\DatabaseConnection;
use NinjaWars\core\data\Player;
use NinjaWars\core\data\Account;
use NinjaWars\core\extensions\SessionFactory;
use NinjaWars\core\extensions\StreamedViewResponse;
use NinjaWars\core\environment\RequestWrapper;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Handle updates for changing account password, changing account email and showing the account page
 */
class AccountController extends AbstractController {
    const ALIVE = false;
    const PRIV  = true;

    /**
     * Show the change email form
     *
     * @return Response
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
     *
     * @return Response
     */
    public function changeEmail() {
        // confirm_delete
        $player     = Player::find(SessionFactory::getSession()->get('player_id'));
        $self_info 	= $player->data();
        $passW 		= RequestWrapper::getPostOrGet('passw', null);
        $username 	= $self_info['uname'];

        $in_newEmail     = trim(RequestWrapper::getPostOrGet('newemail'));
        $in_confirmEmail = trim(RequestWrapper::getPostOrGet('confirmemail'));

        $error = '';
        $successMessage = '';

        $verify = self::is_authentic($username, $passW);

        if ($verify) {
            if ($in_newEmail === $in_confirmEmail) {
                $pos_account = Account::findByEmail($in_newEmail);

                if ($pos_account === null) {
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
     *
     * @return Response
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
     *
     * @return Response
     */
    public function changePassword() {
        $player     = Player::find(SessionFactory::getSession()->get('player_id'));
        $self_info 	= $player->data();
        $passW 		= RequestWrapper::getPostOrGet('passw', null);
        $username 	= $self_info['uname'];

        $in_newPass     = trim(RequestWrapper::getPostOrGet('newpassw'));
        $in_confirmPass = trim(RequestWrapper::getPostOrGet('confirmpassw'));

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
     *
     * @todo Maybe make functionality in the model to have this done.
     * @return void
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
     *
     * @return Response
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
     *
     * @return Response
     */
    public function deleteAccount() {
        $session         = SessionFactory::getSession();
        $player          = Player::find(SessionFactory::getSession()->get('player_id'));
        $self_info       = $player->data();
        $passW           = RequestWrapper::getPostOrGet('passw', null);
        $username        = $self_info['uname'];
        $command         = RequestWrapper::getPostOrGet('command');
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

            return new RedirectResponse('/logout');
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
     *
     * @return Response
     */
    public function index() {
        return $this->render([]);
    }

    /**
     * @return StreamedViewResponse
     */
    private function render($p_parts) {
        $account = Account::findById(SessionFactory::getSession()->get('account_id'));
        $player  = Player::find(SessionFactory::getSession()->get('player_id'));
        $ninjas = $account->getCharacters();

        $parts = [
            'gravatar_url'    => $player->avatarUrl(),
            'player'          => $player->data(),
            'account'         => $account,
            'oauth_provider'  => ($account ? $account->oauth_provider : ''),
            'oauth'           => ($account ? $account->oauth_provider && $account->oauth_id : ''),
            'ninjas'          => $ninjas,
            'successMessage'  => false,
            'error'           => false,
            'command'         => '',
            'delete_attempts' => 0,
        ];

        return new StreamedViewResponse(
            'Your Account',
            'account.tpl',
            array_merge($parts, $p_parts),
            ['quickstat' => 'player']
        );
    }

    /**
     * Just do a check whether the input username and password is valid
     *
     * @return boolean
     */
    public static function is_authentic($p_user, $p_pass) {
        $account = Account::findByLogin($p_user);

        return ($account && $account->authenticate($p_pass));
    }
}
