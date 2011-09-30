<?php
// lib_auth.php

// Check for whether a login and pass match uname/active_email and hash, respectively.
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
		    CASE WHEN accounts.operational THEN 1 ELSE 0 END AS active
			FROM accounts
			JOIN account_players ON account_id = _account_id
			JOIN players ON player_id = _player_id
			WHERE (lower(active_email) = :login
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
			return $result->fetch();
		}
	} else {
		// Update the last login failure timestamp.
		update_last_login_failure(potential_account_id_from_login_username($login));
		return false;
	}
}

/**
 * Login the user and delegate the setup if login is valid.
 **/
function login_user($dirty_user, $p_pass) {
	// Internal function due to it being insecure otherwise.
	// Sets up the environment for a logged in user, using vetted data.
	function _login_user($p_username, $p_player_id, $p_account_id) {
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

	$success = false;
	$error   = 'That password/username combination was incorrect.';

	// Just checks whether the username and password are correct.
	$data = authenticate($dirty_user, $p_pass);

	if (is_array($data)) {
		if ((bool)$data['authenticated']) {
			if ((bool)$data['active']) {
				_login_user($data['uname'], $data['player_id'], $data['account_id']);
				// Block by ip list here, if necessary.
				// *** Set return values ***
				$success = true;
				$error = null;
			} else {	// *** Account was not activated yet ***
				$success = false;
				$error = 'You must activate your account before logging in.';
			}
		}

		// The LOGIN FAILURE case occurs here, and is the default.
	}

	// *** Return array of return values ***
	return array('success' => $success, 'login_error' => $error);
}

// Sets the last logged in date equal to now.
function update_last_logged_in($char_id) {
	$up = "UPDATE accounts SET last_login = now() WHERE account_id = (SELECT _account_id FROM account_players WHERE _player_id = :char_id)";
	return query($up, array(':char_id'=>array($char_id, PDO::PARAM_INT)));
}

// Sets the last failed login date to now()
function update_last_login_failure($account_id) {
	$up = "UPDATE accounts SET last_login_failure = now() WHERE account_id = :account_id";
	return query($up, array(':account_id'=>array($account_id, PDO::PARAM_INT)));
}

// Makes sure that the last login failure was't under a second ago.
function last_login_failure_was_recent($account_id) {
	$query_res = query_item("SELECT CASE WHEN (now() - last_login_failure) < interval '1 second' THEN 1 ELSE 0 END FROM accounts WHERE account_id = :account_id", array(':account_id'=>array($account_id, PDO::PARAM_INT)));
	return ($query_res == 1);
}

// Pull the account_id for any possible username part of the login.
function potential_account_id_from_login_username($login) {
	return query_item('SELECT account_id FROM accounts JOIN account_players ON account_id = _account_id JOIN players ON _player_id = player_id WHERE lower(active_email) = lower(:login1) OR lower(uname) = lower(:login2)', array(':login1'=>$login, ':login2'=>$login));
}


// Simple method to check for player id if you're logged in.
function get_logged_in_char_id() {
	return SESSION::get('player_id');
}

// Wrapper for the account_id function.
function get_logged_in_account_id() {
	return account_id();
}

// Get the account_id as logged in.
function account_id(){
	return SESSION::get('account_id');
}

// Abstraction for getting the account's ip.
function get_account_ip() {
	static $ip;
	if ($ip) {
		return $ip;
	} else {
		return account_info(account_id(), 'ip');
	}
}

// Pull the account_ids for a certain character
function get_char_account_id($char_id){
	return query_item('SELECT account_id from accounts JOIN account_players ON account_id = _account_id 
		where _player_id = :char_id', array(':char_id'=>$char_id));
}

// Check whether two characters have similarities, same account, same ip, etc.
function characters_are_linked($char_id, $char_2_id){
	$account_id = get_char_account_id($char_id);
	$account_2_id = get_char_account_id($char_2_id);
	$char_1_info = char_info($char_id);
	$char_2_info = char_info($char_2_id);
	if(empty($account_id) || empty($account_2_id) || empty($char_1_info) || empty($char_2_info)){
		return false;
	} elseif (isset($char_1_info['active']) && !$char_1_info['active'] ||
		 isset($char_1_info['active']) && !$char_2_info['active']){
		 // Not both of the potential clones are active.
		return false;
	} else {
		if ($account_id == $account_2_id){
			return true;
		}
		$account_ip = account_info($account_id, 'last_ip');
		$account_2_ip = account_info($account_2_id, 'last_ip');
		if(empty($account_ip) || empty($account_2_ip)){
			return false;
		} else {
			return ($account_ip == $account_2_ip);
			// If none of the other stuff matched, then the accounts count as not linked.
		}
	}
}

/**
 * @return boolean Check whether someone is logged into their account.
 **/
function is_logged_in() {
	return !!get_logged_in_account_id();
}

/**
 * Just do a check whether the input username and password is valid
 * @return boolean
 **/
function is_authentic($p_user, $p_pass) {
	$data = authenticate($p_user, $p_pass, false);

	return (isset($data['authenticated']) && (bool)$data['authenticated']);
}

/**
 * Logout function.
 **/
function logout_user() {
	nw_session_destroy();
}

// Signup validation functions.

// Check that the password format fits.
function validate_password($password_to_hash) {
	$error = null;
	if (strlen($password_to_hash) < 7 || strlen($password_to_hash) > 500) {	// *** Why is there a max length to passwords? ***
		$error = 'Phase 2 Incomplete: Passwords must be at least 7 characters long.';
	}

	return $error;
}

// Function for account creation to return the reasons that a username isn't acceptable.
function validate_username($send_name) {
	$error = null;

	if (substr($send_name, 0, 1) != 0 || substr($send_name, 0, 1) == "0") {  // Case the first char isn't a letter???
		$error = "Phase 1 Incomplete: Your ninja name ".$send_name." may not start with a number.\n";
	} else if ($send_name[0] == " ") {  //Checks for a white space at the beginning of the name
		$error = "Phase 1 Incomplete: Your ninja name ".$send_name." may not start with a space.";
	} else if (strlen($send_name) < UNAME_LOWER_LENGTH || strlen($send_name) > UNAME_UPPER_LENGTH) {
		$error = "Phase 1 Incomplete: Your ninja name ".$send_name." must be at least ".UNAME_LOWER_LENGTH." characters and at most ".UNAME_UPPER_LENGTH." characters.";
	} else if ($send_name != htmlentities($send_name)) {
		$error = "Phase 1 Incomplete: Your ninja name ".$send_name." may not contain html.";
	} else if (!username_is_valid($send_name)) {
		//Checks whether the name is different from the html stripped version, or from url-style version, or matches the filter.
		$error = "Phase 1 Incomplete: Your ninja name ".$send_name." should only contain letters, numbers, dashes, and underscores.";
	}

	return $error;
}

/*
 * Username requirements
 * A username must start with a lower-case or upper-case letter
 * A username can contain only letters, numbers, underscores, or dashes.
 * A username must be from 3 to 24 characters long
 * A username cannot end in an underscore
 * A username cannot contain 2 consecutive special characters
 */
function username_is_valid($username) {
	$internal_lower = UNAME_LOWER_LENGTH-2;
	$internal_upper = UNAME_UPPER_LENGTH-2;
	$username = (string)$username;
	return (!preg_match("#[\-_]{2}#", $username) && !preg_match("#[a-z][\-_][\da-z]#i", $username)
		&& preg_match("#^[a-z][\da-z_\-]{".$internal_lower.",".$internal_upper."}[a-z\d]$#i", $username));
}

// Takes in a potential login name and saves it over multiple logins.
function nw_session_start($potential_username = '') {
	$result = array('cookie_created' => false, 'session_existed' => false, 'cookie_existed'=> false);
	if (!isset($_COOKIE['user_cookie']) || $_COOKIE['user_cookie'] != $potential_username) {
		// Refresh cookie if the username isn't set in it yet.
		$result['cookie_created'] = createCookie("user_cookie", $potential_username, (time()+60*60*24*365), "/", WEB_ROOT); // *** 365 days ***
	} else {
		$result['cookie_existed'] = true;
	}

	return $result;
}

// Just to mimic the nw_session_start wrapper.
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
 * Returns display:none style information depending on the current state.
 * Used primarily on the index page.
 **/
function display_when($state) {
	$on  = '';
	$off = "style='display: none;'";
	switch ($state) {
		case 'logged_in':
			return (is_logged_in() ? $on : $off);
			break;
		case 'logged_out':
			return (is_logged_in() ? $off : $on);
			break;
		case 'logout_occurs':
			$logout = in('logout');
			return (isset($logout) ? $on : $off);
			break;
		case 'login_failed':
			return (in('action') == 'login' && !is_logged_in() ? $on : $off);
			break;
		default:
			if (DEBUG) {
				throw Exception('improper display_when() argument');
			} else {
				error_log('improper display_when() argument');
			}
			return $off;
			break;
	}
}

// Canonical source for own name now.
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

// Wrapper to get a char name from a char id.
function get_username($char_id=null) {
	if(defined('DEBUG') && DEBUG && $char_id===null){
		nw_error('Deprecated call to get_char_name(null) with a null argument.  For clarity reasons, this is now deprecated, use self_name() instead.');
	}
	return get_char_name($char_id);
}

// Returns a char name from a char id.
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

// Requires a player id, throwing an exception otherwise.
function player_name_from_id($player_id) {
	if (!$player_id) {
		throw new Exception('Blank player ID to find the username of requested.');
	}

	return get_username($player_id);
}

// Check for own id, migrate to this for calls checking for self.
function self_char_id(){
	static $self_id;
	if ($self_id) {
		return $self_id;
	} else {
		$self_id = get_logged_in_char_id();
		return $self_id;
	}
}

// DEPRECATED: Old named wrapper for get_char_id
function get_user_id($p_name=false) {
	if(defined('DEBUG') && DEBUG && $p_name===false){
		nw_error('Improper call to get_user_id() with no argument.  For clarity reasons, this is now deprecated, use self_char_id() instead.');
	}
	return get_char_id($p_name);
}



// Return the char id that corresponds with a char name, or the logged in account, if no other source is available.
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

// Update activity for a logged in player.
function update_activity_log($p_playerID) {
	// (See update_activity_info in lib_header for the function that updates all the detailed info.)
	DatabaseConnection::getInstance();
	$user_ip = $_SERVER['REMOTE_ADDR'];
	query("UPDATE players SET days = 0, ip = :ip WHERE player_id = :player", array(':ip'=>$user_ip, ':player'=>$p_playerID));
	query("Update accounts set last_ip = :ip, last_login = now() where account_id = (select _account_id from account_players join players on _player_id = player_id where player_id = :pid)",
		array(':ip'=>$user_ip, ':pid'=>$p_playerID));
}

// Simply store whatever authentication info is passed in.
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
 * A better alternative (RFC 2109 compatible) to the php setcookie() function
 *
 * @param string Name of the cookie
 * @param string Value of the cookie
 * @param int Lifetime of the cookie
 * @param string Path where the cookie can be used
 * @param string Domain which can read the cookie
 * @param bool Secure mode?
 * @param bool Only allow HTTP usage?
 * @return bool True or false whether the method has successfully run
 */
function createCookie($name, $value='', $maxage=0, $path='', $domain='', $secure=false, $HTTPOnly=false) {
	$ob = ini_get('output_buffering');
	// Abort the method if headers have already been sent, except when output buffering has been enabled
	if (headers_sent() && (bool) $ob === false || strtolower($ob) == 'off' ) {
		assert("(false) && ('Headers were sent before the cookie was reached, which should not happen.')");
		return false;
	}

	if (!empty($domain)) {
		// Cut off leading http:// or www
		if (strtolower(substr($domain, 0, 7)) == 'http://') $domain = substr($domain, 7);
		// Truncate the domain to accept domains with and without 'www.'.
		if (strtolower(substr($domain, 0, 4)) == 'www.') $domain = substr($domain, 4);
		// Add the dot prefix to ensure compatibility with subdomains
		if (substr($domain, 0, 1) != '.') $domain = '.'.$domain;

		// Remove port information.
		$port = strpos($domain, ':');

		if ($port !== false) $domain = substr($domain, 0, $port);
	}
	// Prevent "headers already sent" error with utf8 support (BOM)
	//if ( utf8_support ) header('Content-Type: text/html; charset=utf-8');
	$header_string = 'Set-Cookie: '.rawurlencode($name).'='.rawurlencode($value)
		.(empty($domain) ? '' : '; Domain='.$domain)
		.(empty($maxage) ? '' : '; Max-Age='.$maxage)
		.(empty($path)   ? '' : '; Path='.$path)
		.(!$secure       ? '' : '; Secure')
		.(!$HTTPOnly     ? '' : '; HttpOnly');
	header($header_string, false);
	assert(isset($domain));
	return true;
}

/*
 * Stats on recent activity and other aggregate counts/information.
 */
function membership_and_combat_stats($update_past_stats=false) {
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
?>
