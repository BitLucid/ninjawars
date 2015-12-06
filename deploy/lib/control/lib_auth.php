<?php
use Symfony\Component\HttpFoundation\Request;

/**
 * Check for whether a login and pass match uname/active_email and hash, respectively.
 */
function authenticate($dirty_login, $p_pass, $limit_login_attempts=true) {
	$login = strtolower(sanitize_to_text((string)$dirty_login));
	$last_login_failure_was_recent = false;
	$pass  = (string)$p_pass;

	if ($limit_login_attempts) {
		$last_login_failure_was_recent = last_login_failure_was_recent(potential_account_id_from_login_username($login));
	}

	if ($login != '' && $pass != '' && !$last_login_failure_was_recent) {
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

		$result = query($sql, array(':login'=>$login, ':pass'=>$pass));

		if ($result->rowCount() < 1) {	// *** No record was found, user does not exist ***
			update_last_login_failure(potential_account_id_from_login_username($login));
			return false;
		} else {
			if($result->rowCount()>1){
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
 */
function _login_user($p_username, $p_player_id, $p_account_id) {
	if(!$p_username || !$p_player_id || !$p_account_id){
		throw new Exception('Request made to _login_user without all of username, player_id, and account_id being set.');
	}
    SESSION::commence(); // Start a session on a successful login.
    $_COOKIE['username'] = $p_username; // May want to keep this for relogin easing purposes.
    SESSION::set('username', $p_username); // Actually char name
    SESSION::set('player_id', $p_player_id); // Actually char id.
    SESSION::set('account_id', $p_account_id);
    update_activity_log($p_player_id);
    update_last_logged_in($p_player_id);
    $up = "UPDATE players SET active = 1 WHERE player_id = :char_id";
    query($up, array(':char_id'=>array($p_player_id, PDO::PARAM_INT)));
}

/**
 * Login the user and delegate the setup if login is valid.
 */
function login_user($dirty_user, $p_pass) {
	// Internal function due to it being insecure otherwise.

    if(!function_exists('_login_user')){
    }

	$success = false;
	$login_error   = 'That password/username combination was incorrect.';
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
				$login_error = "You must confirm your account before logging in, check your email. <a href='/account_issues.php'>You can request another confirmation email here.</a>";
			}
		}
		// The LOGIN FAILURE case occurs here, and is the default.
	}
	// *** Return array of return values ***
	return array('success' => $success, 'login_error' => $login_error);
}

/**
 * Login a user via a pre-authenticated oauth id.
 */
function login_user_by_oauth($oauth_id, $oauth_provider){
	$account_info = query_row('select players.player_id, players.uname, accounts.account_id
		from players left join account_players on players.player_id = account_players._player_id
		left join accounts on accounts.account_id = account_players._account_id
		where accounts.oauth_provider = :oauth_provider and accounts.oauth_id = :oauth_id and accounts.operational limit 1',
		array(':oauth_provider'=>$oauth_provider, ':oauth_id'=>$oauth_id));
	$username = $account_info['uname'];
	$player_id = $account_info['player_id'];
	$account_id = $account_info['account_id'];
	$success = false;
	$login_error = 'Sorry, that '.$oauth_provider.' account is not yet connected to a ninjawars account.';
	if($username && $player_id && $account_id){
		_login_user($username, $player_id, $account_id);
		$success = true;
		$login_error = null;
	}
	return array('success' => $success, 'login_error' => $login_error);
}

/**
 * Perform all the login functionality for the login page as requested.
 */
function perform_login_if_requested($username_requested, $pass) {
	Request::setTrustedProxies(Constants::$trusted_proxies);
	$request = Request::createFromGlobals();

	$user_agent = (isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : null);

	$login_attempt_info = array(
		'username'        => $username_requested,
		'user_agent'      => $user_agent,
		'ip'              => $request->getClientIp(),
		'successful'      => 0,
		'additional_info' => $_SERVER
	);

	$logged_in    = login_user($username_requested, $pass);
	$is_logged_in = $logged_in['success'];

	if (!$is_logged_in) { // Login was attempted, but failed, so display an error.
		store_auth_attempt($login_attempt_info);
		$login_error_message = $logged_in['login_error'];
		return $login_error_message;
	} else {
		// log a successful login attempt
		$login_attempt_info['successful'] = 1;
		store_auth_attempt($login_attempt_info);
		return '';
	}
}

/**
 * Get the account that matches an oauth provider.
 */
function find_account_info_by_oauth($id, $provider='facebook'){
	$id = positive_int($id);
	$account_info = query_row('select * from accounts where ( oauth_id = :id and oauth_provider = :provider )
		order by operational, type, created_date asc limit 1',
array(':id'=>$id, ':provider'=>$provider));
if(empty($account_info) || !$account_info['account_id']){
	return false;
} else {
	return $account_info;
}
}

/**
 * Add oauth to an account.
 */
function add_oauth_to_account($account_id, $oauth_id, $oauth_provider='facebook'){
	$res = query('update accounts set oauth_id = :oauth_id, oauth_provider = :oauth_provider where account_id = :account_id',
		array(':oauth_id'=>$oauth_id, ':oauth_provider'=>$oauth_provider, ':account_id'=>$account_id));
	return ($res->rowCount()>0);
}

/**
 * Sets the last logged in date equal to now.
 */
function update_last_logged_in($char_id) {
	$up = "UPDATE accounts SET last_login = now() WHERE account_id = (SELECT _account_id FROM account_players WHERE _player_id = :char_id)";
	return query($up, array(':char_id'=>array($char_id, PDO::PARAM_INT)));
}

/**
 * Sets the last failed login date to now()
 */
function update_last_login_failure($account_id) {
	$up = "UPDATE accounts SET last_login_failure = now() WHERE account_id = :account_id";
	return query($up, array(':account_id'=>array($account_id, PDO::PARAM_INT)));
}

/**
 * Makes sure that the last login failure was't under a second ago.
 */
function last_login_failure_was_recent($account_id) {
	$query_res = query_item("SELECT CASE WHEN (now() - last_login_failure) < interval '1 second' THEN 1 ELSE 0 END FROM accounts WHERE account_id = :account_id", array(':account_id'=>array($account_id, PDO::PARAM_INT)));
	return ($query_res == 1);
}

/**
 * Pull the account_id for any possible username part of the login.
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
 * Simple method to check for player id if you're logged in.
 */
function get_logged_in_char_id() {
	return SESSION::get('player_id');
}

/**
 * Get the account_id as logged in.
 */
function account_id(){
	return SESSION::get('account_id');
}

/**
 * Pull the account_ids for a certain character
 */
function account_id_by_ninja_id($ninja_id){
	return query_item('SELECT account_id from accounts JOIN account_players ON account_id = _account_id
		where _player_id = :ninja_id', array(':ninja_id'=>$ninja_id));
}

/**
 * Check whether two characters have similarities, same account, same ip, etc.
 */
function characters_are_linked($char_id, $char_2_id){
	$account_id = account_id_by_ninja_id($char_id);
	$account_2_id = account_id_by_ninja_id($char_2_id);
	$char_1_info = char_info($char_id);
	$char_1_active = @$char_1_info['active'];
	$char_2_info = char_info($char_2_id);
	$char_2_active = @$char_2_info['active'];
	$server_ip = $_SERVER['SERVER_ADDR'];
	$allowed_ips = array_merge(['127.0.0.1', $server_ip], Constants::$trusted_proxies);
	if(empty($account_id) || empty($account_2_id) || empty($char_1_info) || empty($char_2_info)){
		return false;
	} elseif (!$char_1_active || !$char_2_active){
		 // Not both of the potential clones are active.
		return false;
	} else {
		if ($account_id == $account_2_id){
			error_log('Two accounts were linked ['.$account_id.'] and ['.$account_2_id.']');
			return true;
		}
		$account_ip = account_info($account_id, 'last_ip');
		$account_2_ip = account_info($account_2_id, 'last_ip');
		if(empty($account_ip) || empty($account_2_ip) || in_array($account_ip, $allowed_ips) || in_array($account_2_ip, $allowed_ips)){
			// When account ips are empty or equal the server ip, then don't clone kill them.
			return false;
		} else {
			error_log('Two accounts were linked ['.$account_id.'] and ['.$account_2_id.'] with ips ['.$account_ip.'] and ['.$account_2_ip.']');
			return ($account_ip == $account_2_ip);
			// If none of the other stuff matched, then the accounts count as not linked.
		}
	}
}

/**
 * Check whether someone is logged into their account.
 *
 * @return boolean
 */
function is_logged_in() {
	return (bool) account_id();
}

/**
 * Just do a check whether the input username and password is valid
 *
 * @return boolean
 */
function is_authentic($p_user, $p_pass) {
	$data = authenticate($p_user, $p_pass, false);

	return (isset($data['authenticated']) && (bool)$data['authenticated']);
}

/**
 * Logout function.
 */
function logout_user() {
	nw_session_destroy();
}

/**
 * Check that the password format fits.
 */
function validate_password($password_to_hash) {
	$error = null;
	if (strlen($password_to_hash) < 3 || strlen($password_to_hash) > 500) {	// *** Why is there a max length to passwords? ***
		$error = 'Phase 2 Incomplete: Passwords must be at least 3 characters long.';
	}

	return $error;
}

/**
 * Function for account creation to return the reasons that a username isn't acceptable.
 */
function validate_username($send_name) {
	$error = null;
	$format_error = username_format_validate($send_name);
	if($format_error){
		$error = 'Phase 1 Incomplete: Ninja name: '.$error;
	}
	return $error;
}

/**
 * Just a simple wrapper to turn the presence of a username format error into a boolean check
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
 */
function username_format_validate($username){
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
 * Just to mimic the nw_session_start wrapper.
 */
function nw_session_destroy() {
	if(!isset($_SESSION)){session_start();}
	session_regenerate_id();
	session_unset();
	// Unset all of the session variables.
	$_SESSION = array();

	// If it's desired to kill the session, also delete the session cookie.
	// Note: This will destroy the session, and not just the session data!
	if (ini_get("session.use_cookies")) {
		$params = session_get_cookie_params();
		setcookie(session_name(), '', time() - 42000,
		    $params["path"], $params["domain"],
		    $params["secure"], $params["httponly"]
		);
	}

	// Finally, destroy the session.
	session_destroy();
}

/**
 * Canonical source for own name now.
 */
function self_name(){
	static $self;
	if ($self) {
		// Self info requested
		return $self;
	} else {
		// Determine & store username.
		$char_id = get_logged_in_char_id();
		$sql = "SELECT uname FROM players WHERE player_id = :player";
		$username = query_item($sql, array(':player'=>$char_id));
		$self = $username; // Store it for later.
		return $self;
	}
}

/**
 * Wrapper to get a char name from a char id.
 */
function get_username($char_id=null) {
	if(defined('DEBUG') && DEBUG && $char_id===null){
		nw_error('Deprecated call to get_char_name(null) with a null argument.  For clarity reasons, this is now deprecated, use self_name() instead.');
	}
	return get_char_name($char_id);
}

/**
 * Returns a char name from a char id.
 */
function get_char_name($char_id=null) {
	if ($char_id === null) {
		if(defined('DEBUG') && DEBUG && $char_id===null){
			nw_error('Deprecated call to get_char_name(null) with a null argument.  For clarity reasons, this is now deprecated, use self_name() instead.');
		}
		return self_name();
	} else {
		// Determine some other character's username and return it.
		$sql = "SELECT uname FROM players WHERE player_id = :player";
		return query_item($sql, array(':player'=>$char_id));
	}
}

/**
 * Check for own id, migrate to this for calls checking for self.
 */
function self_char_id(){
	static $self_id;
	if ($self_id) {
		return $self_id;
	} else {
		$self_id = get_logged_in_char_id();
		return $self_id;
	}
}

/**
 * DEPRECATED: Old named wrapper for get_char_id
 */
function get_user_id($p_name=false) {
	if(defined('DEBUG') && DEBUG && $p_name===false){
		nw_error('Improper call to get_user_id() with no argument.  For clarity reasons, this is now deprecated, use self_char_id() instead.');
	}
	return get_char_id($p_name);
}

/**
 * Return the char id that corresponds with a char name, or the logged in account, if no other source is available.
 */
function get_char_id($p_name=false) {
	if ($p_name === false) {
		if(defined('DEBUG') && DEBUG){
			nw_error('Improper call to get_char_id with a null argument.  For clarity reasons, this is now deprecated, use self_char_id() instead.');
		}
		return self_char_id(); // TODO: Remove this use case, it's troublesome.
	} else {
		if($p_name){
			$sql = "SELECT player_id FROM players WHERE lower(uname) = :find";
			return query_item($sql, array(':find'=>strtolower($p_name)));
		} else {
			return null; // a blank name came in, or a name
		}

	}
}

/**
 * Get the ninja id for a ninja name
 */
function ninja_id($name){
	$find = 'select player_id from players where lower(uname) = :name';
	return query_item($find, array(':name'=>strtolower($name)));
}

/**
 * Update activity for a logged in player.
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

/**
 * Simply store whatever authentication info is passed in.
 */
function store_auth_attempt($info){
	// Simply log the attempts in the database.
	$additional_info = null;
	if($info['additional_info']){
		// Encode all the info from $_SERVER, for now.
		$additional_info = json_encode($info['additional_info']);
	}
	if(!$info['successful']){
		// Update last login failure.
		update_last_login_failure(potential_account_id_from_login_username($info['username']));
	}
	// Log the login attempt as well.
	query("insert into login_attempts (username, ua_string, ip, successful, additional_info) values (:username, :user_agent, :ip, :successful, :additional_info)", array(':username'=>$info['username'], ':user_agent'=>$info['user_agent'], ':ip'=>$info['ip'], ':successful'=>$info['successful'], ':additional_info'=>$additional_info));
}

/**
 * Stats on recent activity and other aggregate counts/information.
 */
function membership_and_combat_stats() {
	DatabaseConnection::getInstance();
	$vk = DatabaseConnection::$pdo->query('SELECT stat_result from past_stats where id = 4');
	$todaysViciousKiller = $vk->fetchColumn();

	$stats['vicious_killer'] = $todaysViciousKiller;
	$pc = DatabaseConnection::$pdo->query("SELECT count(player_id) FROM players WHERE active = 1");
	$stats['player_count'] = $pc->fetchColumn();

	$po = DatabaseConnection::$pdo->query("SELECT count(*) FROM ppl_online WHERE member = true");
	$stats['players_online'] = $po->fetchColumn();

	$stats['active_chars'] = query_item("SELECT count(*) FROM ppl_online WHERE member = true AND activity > (now() - CAST('15 minutes' AS interval))");
	return $stats;
}
