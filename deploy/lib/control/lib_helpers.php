<?php
use NinjaWars\core\data\DatabaseConnection;
use NinjaWars\core\data\Player;
use NinjaWars\core\extensions\SessionFactory;
use NinjaWars\core\environment\RequestWrapper;
use Symfony\Component\HttpFoundation\Request;
use NinjaWars\core\Filter;

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
		$statement->bindValue(':member', SessionFactory::getSession()->get('authenticated', false), PDO::PARAM_BOOL);
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
	if ($filter_callback) {
		$result = Filter::$filter_callback($result);
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

function nw_debug() {
	$result = false;

	if (DEBUG) {
		$result = true;
	}

	if ($_COOKIE['debug']) {
		$result = true;
	}

	return $result;
}
