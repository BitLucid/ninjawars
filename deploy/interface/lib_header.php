<?php
/**
 * Update the information of a viewing observer, or player.
**/
function update_activity_info()
{
	// ******************** Usage Information of the browser *********************
	$remoteAddress = (isset($_SERVER['REMOTE_ADDR'])     ? $_SERVER['REMOTE_ADDR']     : NULL);
	$userAgent     = (isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : NULL);
	$referer       = (isset($_SERVER['HTTP_REFERER'])    ? $_SERVER['HTTP_REFERER']    : NULL);

	// ************** Setting anonymous and player usage information

	$dbconn = new DatabaseConnection();

	if (!SESSION::is_set('online'))
	{	// *** Completely new session, update latest activity log. ***
		if ($remoteAddress)
		{	// *** Delete prior to trying to re-insert into the people online. ***
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
	}
	else
	{	// *** An already existing session. ***
		$statement = DatabaseConnection::$pdo->prepare('UPDATE ppl_online SET activity = now(), member = :member WHERE session_id = :sessionID');
		$statement->bindValue(':sessionID', session_id());
		$statement->bindValue(':member', is_logged_in(), PDO::PARAM_BOOL);
		$statement->execute();
	}
}

/**
 * Writes out the header for all the pages.
 * Will need a "don't write header" option for jQuery iframes.
**/
function render_html_for_header($p_title = null, $p_bodyClasses = 'body-default')
{
	$parts = array(
		'title'          => ($p_title ? htmlentities($p_title) : '')
		, 'body_classes' => $p_bodyClasses
		, 'WEB_ROOT'     => WEB_ROOT
		, 'local_js'     => (OFFLINE || DEBUG)
		, 'DEBUG'        => DEBUG
	);

	return render_template('header.tpl', $parts);
}

// Renders the error message when a section isn't viewable.
function render_viewable_error($p_error)
{
	return render_template("error.tpl", array('error'=>$p_error));
}

/**
 * Returns the state of the player from the database,
 * defaults to the currently logged in player, but can act on any player
 * if another username is passed in.
**/
function get_player_info($p_id = null)
{
	$dao = new PlayerDAO();
	$id = either($p_id, $_SESSION['player_id']); // *** Default to current. ***
	$playerVO = $dao->get($id);

	$player_data = array();

	foreach ($playerVO as $fieldName=>$value)
	{
		$player_data[$fieldName] = $value;
	}

	///TODO: Migrate all calls of this function to a new function that returns a Player object. When all calls to this function are removed, remove this function
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
