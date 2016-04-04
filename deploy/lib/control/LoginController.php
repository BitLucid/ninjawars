<?php
namespace NinjaWars\core\control;

use NinjaWars\core\data\Account;
use NinjaWars\core\extensions\SessionFactory;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use \Constants;

/**
 */
class LoginController {
    const ALIVE = false;
    const PRIV  = false;

    /**
     * Try to perform a login, perform_login_if_requested will redirect as necessary
     */
    public function requestLogin() {
        $logged_out          = in('logged_out'); // Logout page redirected to this one, so display the message.
        $login_error_message = in('error'); // Error to display after unsuccessful login and redirection.
        $pass                = post('pass');
        $username_requested  = post('user');

        if ($logged_out) {
            logout_user(); // Perform logout if requested!
        } else if ($username_requested === null || $pass === null) {
            $login_error_message = 'No username or no password specified';
        }

        if (!$login_error_message && !SessionFactory::getSession()->get('authenticated', false)) {
            $login_error_message = self::perform_login_if_requested($username_requested, $pass);
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
        $logged_out          = in('logged_out'); // Logout page redirected to this one, so display the message.
        $login_error_message = in('error'); // Error to display after unsuccessful login and redirection.

        $stored_username = (isset($_COOKIE['username']) ? $_COOKIE['username'] : null);
        $referrer        = (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null);

        if (SessionFactory::getSession()->get('authenticated', false)) {
            return new RedirectResponse(WEB_ROOT);
        } else {
            $parts = [
                'authenticated'       => false,
                'login_error_message' => $login_error_message,
                'logged_out'          => $logged_out,
                'referrer'            => $referrer,
                'stored_username'     => $stored_username,
            ];

            return $this->render('Login', $parts);
        }
    }

    /**
     * Render the concrete elements of the response
     */
    public function render($title, $parts) {
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
    public static function perform_login_if_requested($username_requested, $pass) {
        Request::setTrustedProxies(Constants::$trusted_proxies);
        $request = Request::createFromGlobals();

        $user_agent = (isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : null);

        $login_attempt_info = [
            'username'        => $username_requested,
            'user_agent'      => $user_agent,
            'ip'              => $request->getClientIp(),
            'successful'      => 0,
            'additional_info' => $_SERVER
        ];

        $logged_in    = login_user($username_requested, $pass);

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
            $account = Account::findById(potential_account_id_from_login_username($info['username']));

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
}
