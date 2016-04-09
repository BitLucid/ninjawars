<?php
namespace NinjaWars\core\control;

use NinjaWars\core\control\AbstractController;
use NinjaWars\core\data\Account;
use NinjaWars\core\data\Player;
use NinjaWars\core\environment\RequestWrapper;
use NinjaWars\core\extensions\SessionFactory;
use Symfony\Component\HttpFoundation\RedirectResponse;
use \Constants;
use \PDO;

/**
 */
class LoginController extends AbstractController {
    const ALIVE = false;
    const PRIV  = false;

    /**
     * Try to perform a login
     */
    public function requestLogin() {
        $login_error_message = in('error'); // Error to display after unsuccessful login and redirection.
        $pass                = post('pass');
        $username_requested  = post('user');

        if ($username_requested === null || $pass === null) {
            $login_error_message = 'No username or no password specified';
        }

        if (!$login_error_message && !SessionFactory::getSession()->get('authenticated', false)) {
            $login_error_message = $this->performLogin($username_requested, $pass);
        }

        if ($login_error_message) {
            return new RedirectResponse('/login.php?error='.rawurlencode($login_error_message));
        } else { // Successful login, go to the main page
            return new RedirectResponse('/');
        }
    }

    /**
     * Display standard login page.
     */
    public function index() {
        $login_error_message = in('error'); // Error to display after unsuccessful login and redirection.

        $stored_username = (isset($_COOKIE['username']) ? $_COOKIE['username'] : null);
        $referrer        = (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null);

        if (SessionFactory::getSession()->get('authenticated', false)) {
            return new RedirectResponse(WEB_ROOT);
        } else {
            $parts = [
                'authenticated'       => false,
                'login_error_message' => $login_error_message,
                'referrer'            => $referrer,
                'stored_username'     => $stored_username,
            ];

            return $this->render('Login', $parts);
        }
    }

    /**
     * Render the concrete elements of the response
     */
    private function render($title, $parts) {
        $response = [
            'template' => 'login.tpl',
            'title'    => $title,
            'parts'    => $parts,
            'options'  => []
        ];

        return $response;
    }

    /**
     * Perform all the login functionality for the login page as requested.
     */
    public function performLogin($username_requested, $pass) {
        RequestWrapper::init();
        $request = RequestWrapper::$request;

        $user_agent = (isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : null);

        $login_attempt_info = [
            'username'        => $username_requested,
            'user_agent'      => $user_agent,
            'ip'              => $request->getClientIp(),
            'successful'      => 0,
            'additional_info' => $_SERVER
        ];

        $logged_in = $this->loginUser($username_requested, $pass);

        if (!$logged_in['success']) { // Login was attempted, but failed, so display an error.
            self::store_auth_attempt($login_attempt_info);
            $login_error_message = $logged_in['login_error'];

            return $login_error_message;
        } else {
            // log a successful login attempt
            $login_attempt_info['successful'] = 1;
            self::store_auth_attempt($login_attempt_info);

            return '';
        }
    }

    /**
     * Simply store whatever authentication info is passed in.
     */
    public static function store_auth_attempt($info) {
        // Simply log the attempts in the database.
        $additional_info = null;

        if ($info['additional_info']) {
            // Encode all the info from $_SERVER, for now.
            $additional_info = json_encode($info['additional_info']);
        }

        if (!$info['successful']) {
            // Update last login failure.
            $account = Account::findByLogin($info['username']);

            if ($account) {
                Account::updateLastLoginFailure($account);
            }
        }

        // Log the login attempt as well.
        query(
            "INSERT INTO login_attempts
            (username, ua_string, ip, successful, additional_info)
            VALUES
            (:username, :user_agent, :ip, :successful, :additional_info)",
            [
                ':username'        => $info['username'],
                ':user_agent'      => $info['user_agent'],
                ':ip'              => $info['ip'],
                ':successful'      => $info['successful'],
                ':additional_info' => $additional_info,
            ]
        );
    }

    /**
     * Login the user and delegate the setup if login is valid.
     *
     * @return array
     */
    private function loginUser($dirty_user, $p_pass) {
        $success = false;
        $login_error = 'That password/username combination was incorrect.';
        // Just checks whether the username and password are correct.
        $data = $this->authenticate($dirty_user, $p_pass);

        if (!empty($data)) {
            if ((bool)$data['authenticated'] && (bool)$data['operational']) {
                if ((bool)$data['confirmed']) {
                    $this->createGameSession(Account::findById($data['account_id']), Player::find($data['player_id']));
                    // Block by ip list here, if necessary.
                    // *** Set return values ***
                    $success = true;
                    $login_error = null;
                } else {	// *** Account was not activated yet ***
                    $success = false;
                    $login_error = "You must confirm your account before logging in, check your email. <a href='/assistance'>You can request another confirmation email here.</a>";
                }
            }

            // The LOGIN FAILURE case occurs here, and is the default.
            $account = Account::findByLogin($dirty_user);

            if ($account) {
                Account::updateLastLoginFailure($account);
            }
        }

        // *** Return array of return values ***
        return ['success' => $success, 'login_error' => $login_error];
    }

    /**
     * Actual login!  Performs the login of a user using pre-vetted info!
     *
     * Creates the cookie and session stuff for the login process.
     *
     * @param Account $account
     * @param Player $player
     * @return void
     */
    private function createGameSession(Account $account, Player $player) {
        $_COOKIE['username'] = $player->name();

        $session = SessionFactory::getSession();
        $session->set('username', $player->name());
        $session->set('player_id', $player->id());
        $session->set('account_id', $account->id());
        $session->set('authenticated', true);

        RequestWrapper::init();
        $request = RequestWrapper::$request;
        $user_ip = $request->getClientIp();

        query(
            'UPDATE players SET active = 1, days = 0 WHERE player_id = :player',
            [ ':player' => [$player->id(), PDO::PARAM_INT] ]
        );

        query(
            'UPDATE accounts SET last_ip = :ip, last_login = now() WHERE account_id = :account',
            [
                ':ip'      => $user_ip,
                ':account' => [$account->id(), PDO::PARAM_INT],
            ]
        );
    }

    /**
     * Authenticate a set of credentials
     *
     * @return Array
     */
    private function authenticate($dirty_login, $p_pass, $limit_login_attempts=true) {
        $filter_pattern = "/[^\w\d\s_\-\.\@\:\/]/";
        $login          = strtolower(preg_replace($filter_pattern, "", (string)$dirty_login));
        $rate_limit     = false;
        $pass           = (string)$p_pass;
        $account        = Account::findByLogin($login);

        if ($limit_login_attempts && $account) {
            $rate_limit = (intval($account->login_failure_interval) <= 1);
        }

        if ($login != '' && $pass != '' && !$rate_limit) {
            // Pull the account data regardless of whether the password matches,
            // but create an int about whether it does match or not.

            $sql = "SELECT account_id, account_identity, uname, player_id, accounts.confirmed as confirmed,
                CASE WHEN phash = crypt(:pass, phash) THEN 1 ELSE 0 END AS authenticated,
                CASE WHEN accounts.operational THEN 1 ELSE 0 END AS operational
                FROM accounts
                JOIN account_players ON account_id = _account_id
                JOIN players ON player_id = _player_id
                WHERE (active_email = :login OR lower(uname) = :login)";

            $result = query($sql, [':login' => $login, ':pass' => $pass]);

            if ($result->rowCount() < 1) {	// Username does not exist
                return [];
            } else {
                if ($result->rowCount() > 1) {
                    // Just for later reference, check for duplicate usernames via:
                    //select array_accum(uname), count(*) from players group by lower(trim(uname)) having count(*) > 1;
                    error_log('Case-insensitive duplicate username found: '.$login);
                }

                return $result->fetch(); // account found, return results
            }
        } else {
            if ($account) {
                // Update the last login failure timestamp
                Account::updateLastLoginFailure($account);
            }

            return [];
        }
    }
}
