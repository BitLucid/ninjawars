<?php
use NinjaWars\core\data\DatabaseConnection;
use NinjaWars\core\data\Player;
use NinjaWars\core\extensions\SessionFactory;
use NinjaWars\core\environment\RequestWrapper;
use Symfony\Component\HttpFoundation\Request;

/**
 * Return first non-null argument.
 */
function first_value() {
	$arg_list = func_get_args();
	foreach ($arg_list as $l_arg) {
		if (!is_null($l_arg)) {
			return $l_arg;
		}
	}

	return null;
}

/**
 * Much more easy-going, just:
 * Return first true-like argument.
 */
function whichever() {
	$arg_list = func_get_args();
	foreach ($arg_list as $l_arg) {
		if ($l_arg != false) {
			return $l_arg;
		}
	}

	return null;
}

/**
 * Creates all the environmental variables, with no outputting.
 *
 * Places much of the user info into the global namespace.
 */
function init($private, $alive) {
	global $today;
	global $username;
	global $char_id;

	// ******************** Declared variables *****************************
	$today = date("F j, Y, g:i a");  // Today var is only used for creating mails.
	// Page viewing settings usually set before the header.

	update_activity_info(); // *** Updates the activity of the page viewer in the database.

	$error = null;
	$char_id = self_char_id(); // Will default to null.

	if ((!is_logged_in() || !$char_id) && $private) {
		$error = 'log_in';
		// A non-null set of content being in the error triggers a die at the end of the header.
	} elseif ($char_id) {
		// **************** Player information settings. *******************
		global $player, $player_id;
		// Polluting the global namespace here.  Booo.

		$player = Player::find($char_id); // Defaults to current session user.
		$username = $player->name(); // Set the global username.
		$player_id = $player->player_id;

		if ($alive) { // That page requires the player to be alive to view it
			if (!$player->health()) {
				$error = 'dead';
			} else if ($player->hasStatus(FROZEN)) {
				$error = 'frozen';
			}
		}
	}

	return $error;
}

/**
 * Update the information of a viewing observer, or player.
 */
function update_activity_info() {
	// ******************** Usage Information of the browser *********************
	Request::setTrustedProxies(Constants::$trusted_proxies);
	$request = Request::createFromGlobals();
	$remoteAddress = ''.$request->getClientIp();
	$userAgent     = (isset($_SERVER['HTTP_USER_AGENT']) ? substr($_SERVER['HTTP_USER_AGENT'], 0, 250) : NULL); // Truncated at 250 char.
	$referer       = (isset($_SERVER['HTTP_REFERER'])    ? substr($_SERVER['HTTP_REFERER'], 0, 250)    : '');   // Truncated at 250 char.

	// ************** Setting anonymous and player usage information

	DatabaseConnection::getInstance();

	$session = SessionFactory::getSession();

	if (!$session->has('online')) {	// *** Completely new session, update latest activity log. ***
		if ($remoteAddress) {	// *** Delete prior to trying to re-insert into the people online. ***
			$statement = DatabaseConnection::$pdo->prepare('DELETE FROM ppl_online WHERE ip_address = :ip OR session_id = :sessionID');

			$statement->bindValue(':ip',        $remoteAddress);
			$statement->bindValue(':sessionID', $session->getId());

			$statement->execute();
		}

		// *** Update viewer data. ***
		$statement = DatabaseConnection::$pdo->prepare('INSERT INTO ppl_online (session_id, activity, ip_address, refurl, user_agent) VALUES (:sessionID, now(), :ip, :referer, :userAgent)');

		$statement->bindValue(':sessionID', $session->getId());
		$statement->bindValue(':ip',        $remoteAddress);
		$statement->bindValue(':referer',   $referer);
		$statement->bindValue(':userAgent', $userAgent);

		$statement->execute();

		$session->set('online', true);
	} else {	// *** An already existing session. ***
		$statement = DatabaseConnection::$pdo->prepare('UPDATE ppl_online SET activity = now(), member = :member WHERE session_id = :sessionID');
		$statement->bindValue(':sessionID', $session->getId());
		$statement->bindValue(':member', is_logged_in(), PDO::PARAM_BOOL);
		$statement->execute();
	}
}

/**
 * Input function that by default LEAVES INPUT COMPLETELY UNFILTERED
 * To not filter some input, you have to explicitly pass in null for the third parameter,
 * e.g. in('some_url_parameter', null, null)
 */
function in($var_name, $default_val=null, $filter_callback=null) {
	$req = RequestWrapper::getPostOrGet($var_name);
	$result = (isset($req) ? $req : $default_val);

	// Check that the filter function sent in exists.
	if ($filter_callback && function_exists($filter_callback)) {
		$result = $filter_callback($result);
	}

    return $result;
}

/**
 *  Wrapper around the post variables as a clean way to get input.
 */
function post($key, $default_val=null){
	$post = RequestWrapper::getPost($key);
	return isset($post)? $post: $default_val;
}

/**
 * Return a casting with a result of a positive int, or else zero.
 */
function non_negative_int($num) {
	return ((int)$num == $num && (int)$num > 0? (int)$num : 0);
}

/**
 * Casts to an integer anything that can be cast that way non-destructively, otherwise null.
 */
function toInt($dirty) {
	if ($dirty == (int) $dirty) { // Cast anything that can be non-destructively cast.
		$res = (int) $dirty;
	} else {
		$res = null;
	}

	return $res;
}

/**
 * Return a casting with a result of a positive int, or else zero.
 *
 * @Note
 * this function will cast strings with leading integers to those integers.
 * E.g. 555'sql-injection becomes 555
 */
function positive_int($num) {
	return ((int)$num == $num && (int)$num > 0? (int)$num : 0);
}

function debug($val) {
    if (DEBUG) {
    	$vals = func_get_args();
    	foreach($vals as $val){
		    echo "<pre class='debug' style='font-size:12pt;background-color:white;color:black;position:relative;z-index:10'>";
		    var_dump($val);
		    echo "</pre>";
        }
    }
}

function nw_error($message, $level=E_USER_NOTICE) {
	$backtrace = debug_backtrace();
	$caller = next($backtrace);
	$next_caller = next($backtrace);
	trigger_error("<div  class='debug' style='font-size:12pt;background-color:white;color:black;position:relative;z-index:10'>".$message.' in <strong>'.$caller['function'].'</strong> called from <strong>'.$caller['file'].'</strong> on line <strong>'.$caller['line'].'</strong>'."called within: ".$next_caller['function']."\n<br /> and finally from the error handler in lib_dev: </div>", $level);
}
