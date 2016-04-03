<?php
use NinjaWars\core\data\DatabaseConnection;
use NinjaWars\core\extensions\SessionFactory;
use Symfony\Component\HttpFoundation\Request;

/**
 * Check for whether a login and pass match uname/active_email and hash, respectively.
 *
 * @return Array|boolean
 */
function authenticate($dirty_login, $p_pass, $limit_login_attempts=true) {
    $filter_pattern = "/[^\w\d\s_\-\.\@\:\/]/";
    $login = strtolower(preg_replace($filter_pattern, "", (string)$dirty_login));
	$recent_login_failure = false;
	$pass  = (string)$p_pass;

	if ($limit_login_attempts) {
		$recent_login_failure = last_login_failure_was_recent(potential_account_id_from_login_username($login));
	}

	if ($login != '' && $pass != '' && !$recent_login_failure) {
		// Allow login via username or email.

		// Pull the account data regardless of whether the password matches, but create an int about whether it does match or not.
		// matches login string against active_email or username.


		$sql = "SELECT account_id, account_identity, uname, player_id, accounts.confirmed as confirmed,
		    CASE WHEN phash = crypt(:pass, phash) THEN 1 ELSE 0 END AS authenticated,
		    CASE WHEN accounts.operational THEN 1 ELSE 0 END AS operational
			FROM accounts
			JOIN account_players ON account_id = _account_id
			JOIN players ON player_id = _player_id
			WHERE (active_email = :login
					OR lower(uname) = :login)";

		$result = query($sql, [':login' => $login, ':pass' => $pass]);

		if ($result->rowCount() < 1) {	// *** No record was found, user does not exist ***
			update_last_login_failure(potential_account_id_from_login_username($login));
			return false;
		} else {
			if ($result->rowCount() > 1) {
                // Just for later reference, check for duplicate usernames via:
                //select array_accum(uname), count(*) from players group by lower(trim(uname)) having count(*) > 1;
				error_log('Case-insensitive duplicate username found: '.$login);
			}

			return $result->fetch(); // Success, return results.
		}
	} else {
		// Update the last login failure timestamp.
		update_last_login_failure(potential_account_id_from_login_username($login));
		return false;
	}
}

/**
 * Actual login!  Performs the login of a user using pre-vetted info!
 * Creates the cookie and session stuff for the login process.
 *
 * @return void
 */
function _login_user($p_username, $p_player_id, $p_account_id) {
	if (!$p_username || !$p_player_id || !$p_account_id) {
		throw new \Exception('Request made to _login_user without all of username, player_id, and account_id being set.');
	}

    $_COOKIE['username'] = $p_username; // May want to keep this for relogin easing purposes.

    update_activity_log($p_player_id);
    update_last_logged_in($p_player_id);
    $update = "UPDATE players SET active = 1 WHERE player_id = :char_id";
    query($update, array(':char_id'=>array($p_player_id, PDO::PARAM_INT)));

	$session = SessionFactory::getSession();
    $session->set('username', $p_username); // Actually char name
    $session->set('player_id', $p_player_id); // Actually char id.
    $session->set('account_id', $p_account_id);
}

/**
 * Login the user and delegate the setup if login is valid.
 *
 * @return array
 */
function login_user($dirty_user, $p_pass) {
	// Internal function due to it being insecure otherwise.

    if(!function_exists('_login_user')){
    }

	$success = false;
	$login_error = 'That password/username combination was incorrect.';
	// Just checks whether the username and password are correct.
	$data = authenticate($dirty_user, $p_pass);

	if (is_array($data)) {
		if ((bool)$data['authenticated'] && (bool)$data['operational']) {
			if ((bool)$data['confirmed']) {
				_login_user($data['uname'], $data['player_id'], $data['account_id']);
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
	}

	// *** Return array of return values ***
	return ['success' => $success, 'login_error' => $login_error];
}

/**
 * Sets the last logged in date equal to now.
 *
 * @return int
 */
function update_last_logged_in($char_id) {
	$update = "UPDATE accounts SET last_login = now() WHERE account_id = (SELECT _account_id FROM account_players WHERE _player_id = :char_id)";
	return query($update, array(':char_id'=>array($char_id, PDO::PARAM_INT)));
}

/**
 * Sets the last failed login date to now()
 *
 * @return int
 */
function update_last_login_failure($account_id) {
	$update = "UPDATE accounts SET last_login_failure = now() WHERE account_id = :account_id";
	return query($update, array(':account_id'=>array($account_id, PDO::PARAM_INT)));
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
 * Pull the account_id for any possible username part of the login.
 *
 * @return int
 */
function potential_account_id_from_login_username($login) {
	return query_item(
		'SELECT account_id FROM accounts WHERE active_email = :login1
		UNION
		SELECT _account_id AS account_id FROM players JOIN account_players ON player_id = _player_id WHERE lower(uname) = :login2',
		array(
			':login1'=>strtolower($login),
			':login2'=>strtolower($login)
		)
	);
}

/**
 * Check whether someone is logged into their account.
 *
 * @return boolean
 */
function is_logged_in() {
	return (bool)SessionFactory::getSession()->get('account_id');
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
 * Just a simple wrapper to turn the presence of a username format error into a boolean check
 *
 * @return boolean
 */
function username_is_valid($username) {
	// Check for no error from the username_format_validate function.
	return !(bool)username_format_validate($username);
}

/**
 * Return the error reason for a username not validating, if it doesn't.
 *
 * Username requirements (from the username_is_valid() function)
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

/**
 * Check for own id, migrate to this for calls checking for self.
 *
 * @return int
 */
function self_char_id() {
	return SessionFactory::getSession()->get('player_id');
}

/**
 * Update activity for a logged in player.
 *
 * @return void
 */
function update_activity_log($p_playerID) {
    // (See update_activity_info in lib_header for the function that updates all the detailed info.)
    DatabaseConnection::getInstance();
    Request::setTrustedProxies(Constants::$trusted_proxies);
    $request = Request::createFromGlobals();
    $user_ip = $request->getClientIp();
    query("UPDATE players SET days = 0 WHERE player_id = :player", [':player'=>$p_playerID]);
    query("Update accounts set last_ip = :ip, last_login = now() where account_id = (select _account_id from account_players join players on _player_id = player_id where player_id = :pid)",
        array(':ip'=>$user_ip, ':pid'=>$p_playerID));
}

