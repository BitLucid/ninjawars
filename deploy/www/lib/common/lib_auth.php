<?php
require_once(substr(__FILE__,0,(strpos(__FILE__, 'webgame/')))."webgame/lib/base.inc.php");
// lib_auth.php

/**
 * Login the user and delegate the setup if login is valid.
**/
function login_user($user, $pass){
    $filter = new Filter();
    $user = $filter->toUsername($user);
    $pass = $filter->toPassword($pass);
	$success = false;
	$error = 'That password/username combination was incorrect.';
	if($user != '' && $pass != ''){
		$sql = new DBAccess();
		$q = "select uname, player_id from players where lower(uname) = 
			lower('".$user."') and pname = '".$pass."' and confirmed = 1 limit 1";
		$results = $sql->QueryAssoc($q);
		//$q = "select uname, player_id from players where uname = ':user' and pname = ':pass' limit 1";
		//$binds = array(':user' => $user, ':pass' => $pass);
		//$result = $sql->prepared($q, $binds);
		$rows = $sql->getRowCount();
		if(1 == $rows){
			setup_logged_in($results[0]['player_id'], $results[0]['uname']);
			// Set session stuff.
			$success = true;
			$error = '';
		}
	}
	// Redirect if the login doesn't work.
	return array('success' => $success, 'login_error' => $error);
}

/**
 * Just do a check whether the input username and password is valid
 * @return boolean
**/
function is_authentic($user, $pass){
    $filter = new Filter();
    $user = $filter->toUsername($user);
    $pass = $filter->toPassword($pass);
	$res = false;
	if($user != '' && $pass != ''){
		$sql = new DBAccess();
		$q = "select uname, player_id from players where lower(uname) = 
			lower('".$user."') and pname = '".$pass."' and confirmed = 1 limit 1";
		$results = $sql->QueryAssoc($q);
		$rows = $sql->getRowCount();
		if(1 == $rows){
			$res = true;
		}
	}
	return $res;    
}

/**
 * Logout function.
**/
function logout_user($echo=false, $redirect='index.php'){
	$msg = 'You have been logged out.';
	SESSION :: destroy();
	if($echo){
		echo $msg;
	}
	if($redirect){
		redirect($redirect);
	}
}

// Wrapper for the logout_user function above.
function logout($echo=false, $redirect='index.php'){
	return logout_user($echo, $redirect);
}


/**
 * @return boolean Based on the chosen method for deciding whether someone is logged in.
**/
function is_logged_in(){
	if(SESSION :: is_set('username')){
		return true;
	} // else
	return false;
}

/**
 * Sets the extra settings after successful login, does not perform the authentication.
**/
function setup_logged_in($player_id, $username){
	$_COOKIE['username'] = $username;
	SESSION::set('player_id', $player_id);
	SESSION::set('username', $username);
	
	update_activity_log($username);
	// Block by ip list here, if necessary.
	
	$player_data = get_player_info();
	/*
	$players_id = $player_data['player_id'];
	$player_id = $players_id; // Just two aliases for the player id.
	$players_email = $player_data['email'];
	// password and messages intentionally excluded.
	$players_turns    	= $player_data['turns'];
	$players_health   	= $player_data['health'];
	$players_bounty   	= $player_data['bounty'];
	$players_gold     	= $player_data['gold'];
	$players_level    	= $player_data['level'];
	$players_class    	= $player_data['class'];
	$players_strength 	= $player_data['strength'];
	$players_kills		= $player_data['kills'];
	$players_days		= $player_data['days'];
	$players_created_date = $player_data['created_date'];
	$players_last_started_attack = $player_data['last_started_attack'];
	$players_clan 		= $player_data['clan_long_name'];
	// TODO: not ready yet: $players_energy	= $player_data['energy'];
	// Also migrate the player_score to a true player object.
	// Also migrate the rank_id to a true player object.
	$players_status   = getStatus($username);*/
	put_player_info_in_session($player_data);
	
}


function validate_password($send_pass){
	$error = null;
	$filter = new Filter();
	if ($send_pass != htmlentities($send_pass) 
	    || $send_pass != $filter->toPassword($send_pass)){  //  Throws error if password has html elements.
		$error = "Phase 2 Incomplete: Passwords can only have spaces, underscores, numbers, and letters.<hr />\n";
	}
	return $error;
}


function validate_username($send_name){
	$error = null;
	$filter = new Filter();
	if (substr($send_name,0,1)!=0 || substr($send_name,0,1)=="0"){  // Case the first char isn't a letter???
		$error = "Phase 1 Incomplete: Your ninja name ".$send_name." may not start with a number.\n";
	} else if (strlen($send_name) >= 21){   // Case string is greater or equal to 21.
	  	$error = "Phase 1 Incomplete: Your ninja name ".$send_name." may not exceed 20 characters.";
  	} else if ($send_name[0] == " "){  //Checks for a white space at the beginning of the name
		$error = "Phase 1 Incomplete: Your ninja name ".$send_name." may not start with a space.";
	} else if ($send_name != htmlentities($send_name) 
			|| str_replace(" ","%20",$send_name) != urlencode($send_name) 
			|| $send_name != $filter->toUsername($send_name)){
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

function valid_name($username){
    return preg_match("#^[a-z][\da-z_]{6,22}[a-z\d]\$#i", $username);
}
*/


function nw_session_start($potential_username = ''){
	$result = array('cookie_created' => false, 'session_existed' => false, 'cookie_existed'=> false);
	if(!isset($_COOKIE['user_cookie']) || $_COOKIE['user_cookie'] != $potential_username){
		// Refresh cookie if the username isn't set in it yet.
		$result['cookie_created'] =
			createCookie("user_cookie", $potential_username, (time()+60*60*24*365), "/", WEB_ROOT); // *** 360 days ***
	} else {
		$result['cookie_existed'] = true;
	}
	if($potential_username){
		SESSION::set('username', $potential_username);
	} else {
		SESSION::commence();
	}
	return $result;
}


/**
 * Returns display:none style information depending on the current state.
 * Used primarily on the index page.
**/
function display_when($state){
	$on = '';
	$off = "style='display:none;'";
	switch(true){
		case $state == 'logged_in':
			return (is_logged_in()? $on : $off);
		break;
		case $state == 'logged_out':
			return (is_logged_in()? $off : $on);
		break;
		case $state == 'logout_occurs':
			$logout = in('logout');
			return (isset($logout)? $on : $off);
		break;
		case $state == 'login_failed':
			return (in('action') == 'login' && !is_logged_in()? $on : $off);
		break;
		default:
			if(DEBUG){
				throw Exception('improper display_when() argument');
			} else {
				error_log('improper display_when() argument');
			}
			return $off;
		break;  
	}
}



function nw_session_destroy(){
	session_destroy();
}

// Remove this.
function nw_session_set_username($logged_in_username){
	// Indicates successful login. 
	SESSION::set('username', $logged_in_username);
}


function get_username(){
	return (SESSION::is_set('username')? SESSION::get('username') : NULL);
}

function update_activity_log($username){
	$sql = new DBAccess();
	$user_ip = $_SERVER['REMOTE_ADDR'];
	$sql->Update("UPDATE players SET days = 0, ip = '$user_ip' WHERE uname='$username'");
}


// Loops over the array and puts the values into the session.
function put_player_info_in_session($player_stats){
	assert(count($player_stats)>0);
	foreach($player_stats as $name => $val){
		if(is_string($name)){
			SESSION::set($name, $val);
		} else {
			if(DEBUG){
				var_dump($player_stats);
				throw new Exception('player stat not a string');
			}
		}
	}
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
function createCookie($name, $value='', $maxage=0, $path='', $domain='', $secure=false, $HTTPOnly=false)
{
    $ob = ini_get('output_buffering');

    // Abort the method if headers have already been sent, except when output buffering has been enabled
    if ( headers_sent() && (bool) $ob === false || strtolower($ob) == 'off' ){
    	assert("(false) && ('Headers were sent before the cookie was reached, which should not happen.')");
        return false;
    }
    

    if ( !empty($domain) )
    {
    	// Cut off leading http:// or www
    	if ( strtolower( substr($domain, 0, 7) ) == 'http://' ) $domain = substr($domain, 7);
        // Truncate the domain to accept domains with and without 'www.'.
        if ( strtolower( substr($domain, 0, 4) ) == 'www.' ) $domain = substr($domain, 4);
        // Add the dot prefix to ensure compatibility with subdomains
        if ( substr($domain, 0, 1) != '.' ) $domain = '.'.$domain;

        // Remove port information.
        $port = strpos($domain, ':');

        if ( $port !== false ) $domain = substr($domain, 0, $port);
    }

    // Prevent "headers already sent" error with utf8 support (BOM)
    //if ( utf8_support ) header('Content-Type: text/html; charset=utf-8');
	$header_string = 'Set-Cookie: '.rawurlencode($name).'='.rawurlencode($value)
                                .(empty($domain) ? '' : '; Domain='.$domain)
                                .(empty($maxage) ? '' : '; Max-Age='.$maxage)
                                .(empty($path) ? '' : '; Path='.$path)
                                .(!$secure ? '' : '; Secure')
                                .(!$HTTPOnly ? '' : '; HttpOnly');
    //var_dump($header_string);
    header($header_string, false);
	assert(isset($domain));
    return true;
}

/*
 * Stats on recent activity and other aggregate counts/information.
 */
function membership_and_combat_stats($sql, $update_past_stats=false){
	$todaysViciousKiller = $sql->QueryItem('SELECT uname FROM levelling_log 
		WHERE killsdate = now() 
		group by uname, killpoints 
		order by killpoints DESC LIMIT 1'); // *** Gets uname with the most kills today.
	/* $todaysViciousKiller = $sql->QueryItem('SELECT uname FROM levelling_log 
	WHERE killsdate = current_date group by uname order by sum(killpoints) 
	DESC LIMIT 1'); // *** Gets uname with the most kills today.
	*/
	if($todaysViciousKiller == ''){
		$todaysViciousKiller = 'None';
	} elseif ($update_past_stats) {
		$sql->Update('UPDATE past_stats SET stat_result = \''
			.$todaysViciousKiller
			.'\' WHERE id = 4'); // 4 is the ID of the vicious killer stat.
	}
	$stats['vicious_killer'] = $todaysViciousKiller;
	$stats['player_count'] = $sql->QueryItem("Select count(player_id) FROM players WHERE confirmed = 1");
	$stats['players_online'] = $sql->QueryItem("select count(*) from ppl_online where member = true");
	return $stats;
}

?>
