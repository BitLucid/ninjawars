<?php
// lib_auth.php

// Side-effect-less check for whether a login & pass work.
function authenticate($p_login, $p_pass) {
	$login = strtolower((string)$p_login);
	$pass  = strtolower((string)$p_pass);

	if ($login != '' && $pass != '') {
		// Allow login via username or email.
		$sql = "SELECT account_id, account_identity, uname, player_id
			FROM accounts
			JOIN account_players ON account_id = _account_id
			JOIN players ON player_id = _player_id
			WHERE (lower(active_email) = :login OR lower(uname) = :login) AND phash = crypt(:pass, phash)";
		return query_row($sql, array(':login'=>$login, ':pass'=>$pass));
	} else {
		return false;
	}
}

/**
 * Login the user and delegate the setup if login is valid.
 **/
function login_user($p_user, $p_pass) {
	$success = false;
	$error   = 'That password/username combination was incorrect.';

	if ($data = authenticate($p_user, $p_pass)) {
		SESSION::commence(); // Start a session on a successful login.
		$_COOKIE['username'] = $data['uname']; // May want to keep this for relogin easing purposes.
		SESSION::set('username', $data['uname']); // Actually ninja name
		SESSION::set('player_id', $data['player_id']); // Actually ninja id.
		SESSION::set('account_id', $data['account_id']);
		update_activity_log($data['uname']);
		update_last_logged_in($data['player_id']);
		// Block by ip list here, if necessary.
		// *** Set return values ***
		$success = true;
		$error = '';
	}

	// *** Return array of return values ***
	return array('success' => $success, 'login_error' => $error);
}

// Sets the last logged in date equal to now.
function update_last_logged_in($ninja_id) {
	$up = "UPDATE accounts SET last_login = now() WHERE account_id IN (SELECT _account_id FROM account_players WHERE _player_id = :ninja_id)";
	return query($up, array(':ninja_id'=>array($ninja_id, PDO::PARAM_INT)));
}

// Simple method to check for player id if you're logged in.
function get_logged_in_ninja_id() {
	return SESSION::get('player_id');
}

function get_logged_in_account_id() {
	return SESSION::get('account_id');
}

// Abstraction for getting the account's ip.
function get_account_ip() {
	static $ip;

	if ($ip) {
		return $ip;
	} else {
		$info = get_player_info();
		$ip = $info['ip'];
		return $ip;
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
	// Note that authenticate is happily side-effect-less.
	return (boolean)authenticate($p_user, $p_pass);
}

/**
 * Logout function.
 **/
function logout_user($echo=false, $redirect='index.php') {
	$msg = 'You have been logged out.';
	nw_session_destroy();

	if ($echo) {
		echo $msg;
	}

	if ($redirect) {
		redirect($redirect);
	}
}

// Wrapper for the logout_user function above.
function logout($echo=false, $redirect='index.php') {
	return logout_user($echo, $redirect);
}



// Signup validation functions.


// Check that the password format fits.
function validate_password($send_pass) {
	$error = null;
	$filter = new Filter();

	if (strlen($password_to_hash) < 7 || strlen($password_to_hash) > 500) {
		$error = "Phase 2 Incomplete: Passwords must be at least 7 characters long.<hr>\n";
	}

	return $error;
}


function validate_username($send_name) {
	$error = null;
	$filter = new Filter();

	if (substr($send_name, 0, 1) != 0 || substr($send_name, 0, 1) == "0") {  // Case the first char isn't a letter???
		$error = "Phase 1 Incomplete: Your ninja name ".$send_name." may not start with a number.\n";
	} else if (strlen($send_name) >= 21) {   // Case string is greater or equal to 21.
		$error = "Phase 1 Incomplete: Your ninja name ".$send_name." may not exceed 20 characters.";
	} else if ($send_name[0] == " ") {  //Checks for a white space at the beginning of the name
		$error = "Phase 1 Incomplete: Your ninja name ".$send_name." may not start with a space.";
	} else if ($send_name != htmlentities($send_name)
			|| str_replace(" ","%20",$send_name) != urlencode($send_name)
			|| $send_name != $filter->toUsername($send_name)) {
		//Checks whether the name is different from the html stripped version, or from url-style version, or matches the filter.
		$error = "Phase 1 Incomplete: Your ninja name ".$send_name." should only contain letters, numbers, and underscores.";
	}

	return $error;
}

/*
   Potential regex for a username.
 * A username must start with a lower-case or upper-case letter
 * A username can contain only letters, numbers or underscores
 * A username must be between 8 and 24 characters
 * A username cannot end in an underscore

 function valid_name($username) {
 return preg_match("#^[a-z][\da-z_]{6,22}[a-z\d]\$#i", $username);
 }
 */

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

// Wrapper to get a ninja name from a ninja id.
function get_username($ninja_id=null) {
	return get_ninja_name($ninja_id);
}

// Returns a ninja name from a ninja id.
function get_ninja_name($ninja_id=null) {
	static $self;

	if (!$ninja_id) {
		if ($self) {
			// Self info requested
			return $self;
		} else {
			// Determine & store username.
			$ninja_id = get_logged_in_ninja_id();
			$sql = "SELECT uname FROM players WHERE player_id = :player";
			$username = query_item($sql, array(':player'=>$ninja_id));
			$self = $username; // Store it for later.
			return $self;
		}
	} else {
		// Determine some other ninja's username and return it.
		$sql = "SELECT uname FROM players WHERE player_id = :player";
		return query_item($sql, array(':player'=>$user_id));
	}
}

// Requires a player id, throwing an exception otherwise.
function player_name_from_id($player_id) {
	if (!$player_id) {
		throw new Exception('Blank player ID to find the username of requested.');
	}
	return get_username($player_id);
}

// Old named wrapper for get_ninja_id
function get_user_id($p_name=null){
	return get_ninja_id($p_name);
}

// Return the ninja id that corresponds with a ninja name, if no other source is available.
function get_ninja_id($p_name=null) {
	static $self_id; // Store the player's own id.

	if (!$p_name) {
		if ($self_id) {
			return $self_id;
		} else {
			$self_id = get_logged_in_ninja_id();
			return $self_id;
		}
	} else {
		$sql = "SELECT player_id FROM players WHERE lower(uname) = :find";
		return query_item($sql, array(':find'=>strtolower($p_name)));
	}
}

// Update activity for a logged in player.
function update_activity_log($username) {
	// (See update_activity_info in lib_header for the function that updates all the detailed info.)
	DatabaseConnection::getInstance();
	$user_ip = $_SERVER['REMOTE_ADDR'];
	query_resultset("UPDATE players SET days = 0, ip = :ip WHERE uname = :player", array(':ip'=>$user_ip, ':player'=>$username));
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
	$vk = DatabaseConnection::$pdo->query('SELECT uname FROM levelling_log WHERE killsdate = cast(now() AS date) GROUP BY uname, killpoints ORDER BY killpoints DESC LIMIT 1');
	$todaysViciousKiller = $vk->fetchColumn();

	if ($todaysViciousKiller == '') {
		$todaysViciousKiller = 'None';
	} elseif ($update_past_stats) {
		$update = DatabaseConnection::$pdo->prepare('UPDATE past_stats SET stat_result = :visciousKiller WHERE id = 4'); // 4 is the ID of the vicious killer stat.
		$update->bindValue(':visciousKiller', $todaysViciousKiller);
		$update->execute();
	}

	$stats['vicious_killer'] = $todaysViciousKiller;

	$pc = DatabaseConnection::$pdo->query("SELECT count(player_id) FROM players WHERE confirmed = 1");
	$stats['player_count'] = $pc->fetchColumn();

	$po = DatabaseConnection::$pdo->query("SELECT count(*) FROM ppl_online WHERE member = true");
	$stats['players_online'] = $po->fetchColumn();

	return $stats;
}
?>
