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

/**
 * Turn a randomly indexed array into an associatively indexed array.
 */
function array_identity_associate($data, $identity_column='identity') {
    $res = array();
    foreach ($data as $single_row) {
        $loop_identity = $single_row[$identity_column];
        $res[$loop_identity] = $single_row;
    }

    return $res;
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

/**
 */
function update_days() {
	DatabaseConnection::getInstance();
	$players = DatabaseConnection::$pdo->query("UPDATE players SET days = days+1");
	return $players->rowCount();
}

/**
 * Take all characters, and heal them one step closer to their maximum base.
 */
function heal_characters($basic=8, $with_level=true){
	$maximum_heal = Player::maxHealthByLevel(3);
	/*
	Goal:  Faster regen for higher level.
	See the balance sheet:
	https://docs.google.com/spreadsheet/ccc?pli=1&key=0AkoUgtBBP00HdGs0Tmk4bC10TXN0SUJYXzdYMVpFZFE#gid=0
	*/
	$max_hp = Player::maxHealthByLevel(MAX_PLAYER_LEVEL);


	$level_add = '+ cast(floor(level/10) AS int)';
	if(!$with_level){
		$level_add = '';
	}
	// Take level, divide by 10, throw away remainder, and add ten for every whole tenth level.
	// e.g. 99 / 10 = 9.9 floored = 9 * 10 = 90
	$level_limit = 'cast((floor(level /10) * 10) AS int)';
	// Add an amount
	$s = DatabaseConnection::$pdo->prepare(
		"UPDATE players SET health = numeric_smaller(
			(health+:basic ".$level_add."),
			cast((:max_heal + ".$level_limit.") AS int))
		WHERE health > 0 AND NOT cast(status&:poison AS bool) AND health < (:max_heal2 + ".$level_limit.")");
	// Heal a character that is alive, and isn't at their level max yet.
	$s->bindValue(':basic', $basic);
	$s->bindValue(':max_heal', $maximum_heal);
	$s->bindValue(':max_heal2', $maximum_heal);
	$s->bindValue(':poison', POISON);
	$s->execute();
	DatabaseConnection::$pdo->query('COMMIT');
	// Higher levels now heal faster.
	// Higher levels should now also heal to a larger maximum, level dependent.
	// e.g. level 100 gets +100 in how many hitpoints they'll heal up to,
	// level 99 gets +90 in how many hitpoints they'll heal up to.
}

/**
 * Revive up to a small max in minor hours, and a stable percent on major hours.
 * Defaults
 * sample_use: revive_players(array('just_testing'=>true));
 * @param array('minor_revive_to'=>100, 'major_revive_percent'=>5,
 *      'just_testing'=>false)
 */
function revive_players($params=array()) {
	// Previous min/max was 2-4% always, ~3000 players, so 60-120 each time.

	$minor_revive_to      = (isset($params['minor_revive_to']) ? $params['minor_revive_to'] : 100); // minor_revive_to, default 100
	$major_revive_percent = (isset($params['major_revive_percent']) ? $params['major_revive_percent'] : 5); // major_revive_percent, default 5%
	$just_testing         = isset($params['just_testing']);
	$major_hour           = 3; // Hour for the major revive.

	/* General idea should be:
	1: revive to 100
	2: revive to 100 (probably 0)
	3: revive 150, (250 total) to a max of 80% of total, ~2500.
	4: revive to 100 (almost certainly no more)
	5: revive to 100 (almost certainly no more)
	6: revive 150, (400 total) to a max of 80% of total, ~2500
	7: ...etc.
	*/

	// Determine the total dead (& active).
	$sel_dead = DatabaseConnection::$pdo->query('SELECT count(*) FROM players WHERE health < 1 AND active = 1');
	$dead_count = $sel_dead->fetchColumn();

	// If none dead, return false.
	if (!$dead_count) {
		return array(0, 0);
	}

	// Determine the total active.
	$sel_total_active = DatabaseConnection::$pdo->query('SELECT count(*) FROM players WHERE active = 1');
	$total_active = $sel_total_active->fetchColumn();

	// Calc the total alive.
	$total_alive = ($total_active - $dead_count);

	// Determine major or minor based on the hour.
	$sel_current_time = DatabaseConnection::$pdo->query("SELECT amount FROM time WHERE time_label = 'hours'");
	$current_time = $sel_current_time->fetchColumn();
	assert(is_numeric($current_time));

	$major = (($current_time % $major_hour) == 0);

	// If minor, and total_alive is more than minor_revive_to-1, return 0/total.
	if (!$major) { // minor
		if ($total_alive > ($minor_revive_to-1)) { // minor_revive_to already met.
			return array(0, $dead_count);
		} else {  // else revive minor_revive_to - total_alive.
			$revive_amount = floor($minor_revive_to - $total_alive);
		}
	} else { // major.
		$percent_int = floor(($major_revive_percent/100)*$total_active);

		if ($dead_count < $percent_int) {
			// If major, and total_dead is less than target_num (major_revive_percent*total, floored)
			// just revive those that are dead.
			$revive_amount = $dead_count;
		} else {
			// Else revive target_num (major_revive_percent*total, floored)
			$revive_amount = $percent_int;
		}
	}

	assert(isset($revive_amount));
	assert(isset($current_time));
	assert(isset($just_testing));
	assert(isset($dead_count));
	assert(isset($major));
	// Actually perform the revive on those integers.
	// Use the order by clause to determine who revives, by time, days and then by level, using the limit set previously.
	//select uname, player_id, level,floor(($major_revive_percent/100)*$total_active) days, resurrection_time from players where active = 1 AND health < 1 ORDER BY abs(8 - resurrection_time) asc, level desc, days asc
	$select = 'SELECT player_id FROM players WHERE active = 1 AND health < 1 '.
			' ORDER BY abs(:time - resurrection_time) ASC, level DESC, days ASC LIMIT :amount';

	$up_revive_players= 'UPDATE players SET status = 0 ';

	if (!$just_testing) {
		$up_revive_players .= ', health =
							CASE WHEN level >= coalesce(class_skill_level, skill_level)
							THEN (150+(level*3))
							ELSE (100+(level*3)) END
							FROM (SELECT * FROM skill LEFT JOIN class_skill ON skill_id = _skill_id WHERE skill_id = 5)
								AS class_skill ';
							// Midnight heal skill id is the 5.
	}

	$up_revive_players .= ' WHERE player_id IN ('.$select.') ';

	if (!$just_testing) {
		$up_revive_players .= ' AND coalesce(class_skill._class_id, players._class_id) = players._class_id';
	}

	$update = DatabaseConnection::$pdo->prepare($up_revive_players);
	$update->bindValue(':amount', intval($revive_amount));
	$update->bindValue(':time', intval($current_time));
	$update->execute();
	$truly_revived = $update->rowCount();

	// Return the 'revived/total' actually revived.
	return array($truly_revived, $dead_count);
}

