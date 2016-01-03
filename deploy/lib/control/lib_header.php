<?php
use NinjaWars\core\data\DatabaseConnection;
use NinjaWars\core\control\SessionFactory;
use Symfony\Component\HttpFoundation\Request;

/**
  *  Creates all the environmental variables, with no outputting.
**/
function init($private, $alive) {
	global $today;

	// ******************** Declared variables *****************************
	$today = date("F j, Y, g:i a");  // Today var is only used for creating mails.
	// Page viewing settings usually set before the header.

	update_activity_info(); // *** Updates the activity of the page viewer in the database.

	return globalize_user_info($private, $alive); // Sticks lots of user info into the global namespace for backwards compat.
}

// Places much of the user info into the global namespace.
function globalize_user_info($private=true, $alive=true) {
	global $username;
	global $char_id;
	$error = null;
	$char_id = self_char_id(); // Will default to null.

	if ((!is_logged_in() || !$char_id) && $private) {
		$error = 'log_in';
		// A non-null set of content being in the error triggers a die at the end of the header.
	} elseif ($char_id) {
		// **************** Player information settings. *******************
		global $player, $player_id;
		// Polluting the global namespace here.  Booo.

		$player = new Player($char_id); // Defaults to current session user.
		$username = $player->name(); // Set the global username.
		$player_id = $player->player_id;

		assert('isset($player_id)');

		if ($alive) { // *** That page requires the player to be alive to view it.
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
**/
function update_activity_info() {
	// ******************** Usage Information of the browser *********************
	Request::setTrustedProxies(Constants::$trusted_proxies);
	$request = Request::createFromGlobals();
	$remoteAddress = $request->getClientIp();
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
