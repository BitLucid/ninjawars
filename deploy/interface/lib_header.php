<?php


/**
 * Update the information of a viewing observer, or player.
**/
function update_activity_info(){
	$sql = new DBAccess();
	// ******************** Usage Information of the browser *********************
	$remoteAddress = (isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : NULL);
	$userAgent = (isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : NULL);
	$referer = (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : NULL);

	// ************** Setting anonymous and player usage information

	if (!SESSION::is_set('online')) { 	// *** Completely new session, update latest activity log.
	    if($remoteAddress) { // *** Delete prior to trying to re-insert into the people online.
			$sql->query("DELETE FROM ppl_online WHERE ip_address='".$_SERVER['REMOTE_ADDR']."'
				 or session_id = '".session_id()."'");
	    }
	    // Update viewer data.
	  	$sql->query("INSERT INTO ppl_online (session_id, activity, ip_address, refurl, user_agent) ".
		         "VALUES ('".session_id()."', now(), '$remoteAddress', '$referer', '$userAgent')");
	 	SESSION::set('online', true);
	} else { // An already existing session.
		if (is_logged_in()) { // A logged in player, update their activity time
		    $sql->query("UPDATE ppl_online SET activity=now(), member='y' WHERE session_id='".session_id()."'");
		} else { // Un-logged-in observer, as login has not yet occurred.
			$sql->query("UPDATE ppl_online SET activity=now() WHERE session_id='".session_id()."'");
		}
	}
}



/**
 * Writes out the header for all the pages.
 * Will need a "don't write header" option for jQuery iframes.
**/
function render_html_for_header($title=null, $body_classes='body-default', $is_index=null){
	$parts = array(
		'title' => ($title? htmlentities($title) : ''),
		'body_classes'=>$body_classes,
		'WEB_ROOT'=>WEB_ROOT,
		'local_js'=>(OFFLINE || DEBUG? true : false),
		'DEBUG'=>DEBUG,
		'is_index'=>$is_index
	);
	return render_template('header.tpl', $parts);
}


// Renders the error message when a section isn't viewable.
function render_viewable_error($error){
	return render_template("error.tpl", array('error'=>$error));
}


/**
 * Returns the state of the player from the database,
 * uses a user_id if one is present, otherwise
 * defaults to the currently logged in player, but can act on any player
 * if another username is passed in.
 * @param $user user_id or username
 * @param @password Unless true, wipe the password.
**/
function get_player_info($user=null, $password=false){
	$sql = new DBAccess();
	$player_data = null;
	if(is_numeric($user)){
	    $sel_player = "select * from players where player_id = '".$user."' limit 1";
	} else {
    	$username = either($user, SESSION::is_set('username')?SESSION::get('username'):null); // Default to current session user.
    	$sel_player = "select * from players where uname = '".sql($username)."' limit 1";
    }
	$player_data = $sql->QueryRowAssoc($sel_player);
	if(!$password){
	    unset($player_data['pname']);
	}
	return $player_data;
}



/* Potential solution for hash-based in-iframe navigation.
function hash_page_name($page_title=null){
	$page = basename(__FILE__, ".php");
	if ($page && file_exists($page)){
	$page = urlencode($page);
	var_dump($page);
	echo
	<<< EOT
	 <script type="text/javascript">
			if(document.location.hash){
				document.location.hash = '$page';
			}
			</script>
EOT;
	}
}

hash_page_name($page_title);
*/

?>
