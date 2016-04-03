<?php
use NinjaWars\core\data\DatabaseConnection;
use NinjaWars\core\data\Account;
use NinjaWars\core\data\Player;
use NinjaWars\core\extensions\SessionFactory;
use Symfony\Component\HttpFoundation\Request;

/**
 * Authenticate a set of credentials
 *
 * @return Array
 */
function authenticate($dirty_login, $p_pass, $limit_login_attempts=true) {
    $filter_pattern       = "/[^\w\d\s_\-\.\@\:\/]/";
    $login                = strtolower(preg_replace($filter_pattern, "", (string)$dirty_login));
	$recent_login_failure = false;
	$pass                 = (string)$p_pass;
	$rate_limit           = false;
    $account    = Account::findByLogin($login);

	if ($limit_login_attempts && $account) {
		$rate_limit = last_login_failure_was_recent($account->id());
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

/**
 * Actual login!  Performs the login of a user using pre-vetted info!
 *
 * Creates the cookie and session stuff for the login process.
 *
 * @param Account $account
 * @param Player $player
 * @return void
 */
function _login_user(Account $account, Player $player) {
    $_COOKIE['username'] = $player->name();

	$session = SessionFactory::getSession();
    $session->set('username', $player->name());
    $session->set('player_id', $player->id());
    $session->set('account_id', $account->id());
    $session->set('authenticated', true);

    Request::setTrustedProxies(Constants::$trusted_proxies);
    $request = Request::createFromGlobals();
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
 * Login the user and delegate the setup if login is valid.
 *
 * @return array
 */
function login_user($dirty_user, $p_pass) {
	$success = false;
	$login_error = 'That password/username combination was incorrect.';
	// Just checks whether the username and password are correct.
	$data = authenticate($dirty_user, $p_pass);

	if (!empty($data)) {
		if ((bool)$data['authenticated'] && (bool)$data['operational']) {
			if ((bool)$data['confirmed']) {
				_login_user(Account::findById($data['account_id']), Player::find($data['player_id']));
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
 * Makes sure that the last login failure was't under a second ago.
 *
 * @return boolean
 */
function last_login_failure_was_recent($account_id) {
	$query_res = query_item("SELECT CASE WHEN (now() - last_login_failure) < interval '1 second' THEN 1 ELSE 0 END FROM accounts WHERE account_id = :account_id", array(':account_id'=>array($account_id, PDO::PARAM_INT)));
	return ($query_res == 1);
}

/**
 * Logout function.
 *
 * @return void
 */
function logout_user() {
	$session = SessionFactory::getSession();
	$session->clear();
	$session->invalidate();
}

/**
 * Return the error reason for a username not validating, if it doesn't.
 *
 * Username requirements:
 * A username must start with a lower-case or upper-case letter
 * A username can contain only letters, numbers, underscores, or dashes.
 * A username must be from 3 to 24 characters long
 * A username cannot end in an underscore or dash
 * A username cannot contain 2 consecutive special characters
 *
 * @return string|boolean
 */
function username_format_validate($username) {
	$error = false;
	$username = (string) $username;

	if(mb_strlen($username) > UNAME_UPPER_LENGTH){
		$error = 'Name too long. Must be 3 to 24 characters. ';
	} elseif(mb_strlen($username) < UNAME_LOWER_LENGTH){
		$error = 'Name too short. Must be 3 to 24 characters. ';
	}
	if(mb_substr($username, 0, 1, 'utf-8') === '_'){
		$error .= 'Name cannot start with an underscore. ';
	}
	if(mb_substr($username, 0, 1, 'utf-8') === ' '){
		$error .= 'Name cannot start with an space. ';
	}
	if(mb_substr($username, -1, null, 'utf-8') === '_'){
		$error .= 'Name cannot end in an underscore. ';
	}
	if(!preg_match("#[a-z]*#i", $username)){
		$error .= 'Name must start with a letter. ';
	}
	if(!preg_match("#[\da-z\-_]*[a-z0-9]$#i", $username)){
		$error .= 'Name must end with a letter or number. ';
	}
	if(preg_match("#[\-_]{2}#", $username)){
		$error .= 'More than two special characters in a row are not allowed in name. ';
	}
	if(!preg_match("#[\da-z\-_]#i", $username)){
		$error .= 'No special characters except - dash and _ underscore, please. ';
	}
	if(!preg_match("#^[a-z]+([\da-z\-_]+[a-z0-9])?$#iD", $username)){
		$error .= 'Name can only contain letters and numbers, with a dash or underscore or two. ';
	}

	return $error;
}
