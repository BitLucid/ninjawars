<?php
/**
  *  Creates all the environmental variables, with no outputting.
**/
function init($private, $alive) {
	global $today;

	// ******************** Declared variables *****************************
	$today = date("F j, Y, g:i a");  // Today var is only used for creating mails.
	// Page viewing settings usually set before the header.
	$error = null; // Logged in or alive error.

	update_activity_info(); // *** Updates the activity of the page viewer in the database.

	return globalize_user_info($private, $alive); // Sticks lots of user info into the global namespace for backwards compat.
}

/** The breakdown function reversing initialize, should we ever need it.
**
function finalize(){
}*/


// Places much of the user into into the global namespace.
function globalize_user_info($private=true, $alive=true) {
	global $username;
	$error = null;

	$username = get_username(); // Will default to null.

	if ((!is_logged_in() || !$username) && $private) {
		$error = 'log_in';
		// A non-null set of content being in the error triggers a die at the end of the header.
	} elseif ($username) {
		// **************** Player information settings. *******************
		global $player, $player_id, $players_health, $players_level, $players_class;
		// Polluting the global namespace here.  Booo.

		$player = new Player($username); // Defaults to current session user.

		$player_id = $player->player_id;

		assert('isset($player_id)');

		// TODO: Turn this into a list extraction?
		// password and messages intentionally excluded.
		$players_health   	= $player->vo->health;
		$players_level    	= $player->vo->level;
		$players_class    	= $player->vo->class;

		if ($alive) { // *** That page requires the player to be alive to view it.
			if (!$players_health) {
				$error = 'dead';
			} else if ($player->hasStatus(FROZEN)) {
				$error = 'frozen';
			}
		}
	}

	return $error;
}

// Renders an error
function render_error($error) {
	if ($error) { // If there's an error, display that then end.
		$res = $error;
	} else {
		$res = null;
	}

	return $res;
}

/**
 * Update the information of a viewing observer, or player.
**/
function update_activity_info() {
	// ******************** Usage Information of the browser *********************
	$remoteAddress = (isset($_SERVER['REMOTE_ADDR'])     ? $_SERVER['REMOTE_ADDR']                     : NULL);
	$userAgent     = (isset($_SERVER['HTTP_USER_AGENT']) ? substr($_SERVER['HTTP_USER_AGENT'], 0, 250) : NULL); // Truncated at 250 char.
	$referer       = (isset($_SERVER['HTTP_REFERER'])    ? substr($_SERVER['HTTP_REFERER'], 0, 250)    : '');   // Truncated at 250 char.

	// ************** Setting anonymous and player usage information

	$dbconn = DatabaseConnection::getInstance();

	if (!SESSION::is_set('online')) {	// *** Completely new session, update latest activity log. ***
		if ($remoteAddress) {	// *** Delete prior to trying to re-insert into the people online. ***
			$statement = DatabaseConnection::$pdo->prepare('DELETE FROM ppl_online WHERE ip_address = :ip OR session_id = :sessionID');

			$statement->bindValue(':ip',        $remoteAddress);
			$statement->bindValue(':sessionID', session_id());

			$statement->execute();
		}

		// *** Update viewer data. ***
		$statement = DatabaseConnection::$pdo->prepare('INSERT INTO ppl_online (session_id, activity, ip_address, refurl, user_agent) VALUES (:sessionID, now(), :ip, :referer, :userAgent)');

		$statement->bindValue(':sessionID', session_id());
		$statement->bindValue(':ip',        $remoteAddress);
		$statement->bindValue(':referer',   $referer);
		$statement->bindValue(':userAgent', $userAgent);

		$statement->execute();

		SESSION::set('online', true);
	} else {	// *** An already existing session. ***
		$statement = DatabaseConnection::$pdo->prepare('UPDATE ppl_online SET activity = now(), member = :member WHERE session_id = :sessionID');
		$statement->bindValue(':sessionID', session_id());
		$statement->bindValue(':member', is_logged_in(), PDO::PARAM_BOOL);
		$statement->execute();
	}
}

/**
 * Returns the state of the player from the database,
 * uses a user_id if one is present, otherwise
 * defaults to the currently logged in player, but can act on any player
 * if another username is passed in.
 * @param $user user_id or username
 * @param @password Unless true, wipe the password.
**/
function get_player_info($p_id = null, $p_password = false) {
	$dao = new PlayerDAO();
	$id = whichever($p_id, SESSION::get('player_id')); // *** Default to current player. ***

	$playerVO = $dao->get($id);

	$player_data = array();

	if ($playerVO) {
		foreach ($playerVO as $fieldName=>$value) {
			$player_data[$fieldName] = $value;
		}

		if (!$p_password) {
			unset($player_data['pname']);
		}
	}

	///TODO: Migrate all calls of this function to a new function that returns an arrayizable Player object. 
	//When all calls to this function are removed, remove this function
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
