<?php
/*
 * Functions for use in the deities.
 *
 * @package deity
 * @subpackage deity_lib
 */

function delete_old_messages($limit = null) {
	DatabaseConnection::getInstance();
	$interval_to_keep = '3 months';
	$statement = DatabaseConnection::$pdo->query("delete from messages where date < ( now()- interval '$interval_to_keep' )");
	return $statement->rowCount();
}

function delete_old_events($limit = null) {
	DatabaseConnection::getInstance();
	$interval_to_keep = '4 days';
	$statement = DatabaseConnection::$pdo->query("delete from events where date < ( now()- interval '$interval_to_keep' )");
	return $statement->rowCount();
}

function update_days() {
	DatabaseConnection::getInstance();
	$players = DatabaseConnection::$pdo->query("UPDATE players SET days = days+1");
	return $players->rowCount();
}

// Chats stuff.
function shorten_chat($message_limit=800) {
	DatabaseConnection::getInstance();
	// Find the latest 800 messages and delete all the rest;
	$deleted = DatabaseConnection::$pdo->query("delete from chat where chat_id not in (select chat_id from chat order by date desc limit ".$message_limit.")");  //Deletes old chat messages.
	return (int) $deleted->rowCount();
}

function unconfirm_older_players_over_minimums($keep_players=2300, $unconfirm_days_over=90, $max_to_unconfirm=30, $just_testing=true) {
	$change_confirm_to = ($just_testing ? '1' : '0'); // Only unconfirm players when not testing.
	$minimum_days = 30;
	$max_to_unconfirm = (is_numeric($max_to_unconfirm) ? $max_to_unconfirm : 30);
	$players_unconfirmed = null;
	DatabaseConnection::getInstance();
	$sel_cur = DatabaseConnection::$pdo->query("select count(*) from players where confirmed = 1");
	$current_players = $sel_cur->fetchColumn();

	if ($current_players < $keep_players) {
		// *** If we're under the minimum, don't unconfirm anyone.
		return false;
	}

	// *** Don't unconfirm anyone below the minimum floor.
	$unconfirm_days_over = max($unconfirm_days_over, $minimum_days);

	// Unconfirm at a maximum of 20 players at a time.
	$unconfirm_within_limits = "UPDATE players
		SET confirmed = ".$change_confirm_to."
		WHERE players.player_id
		IN (
			SELECT player_id FROM players
			WHERE confirmed = 1
			AND days > ".intval($unconfirm_days_over)."
			ORDER BY player_id DESC	LIMIT ".$max_to_unconfirm."
			)";
	$update = DatabaseConnection::$pdo->query($unconfirm_within_limits);
	return $update->rowCount();
}

/**
 * Revive up to a small max in minor hours, and a stable percent on major hours.
 * Defaults
 * sample_use: revive_players(array('just_testing'=>true));
 * @params array('full_max'=>80, 'minor_revive_to'=>100, 'major_revive_percent'=>5,
 *      'just_testing'=>false)
**/
function revive_players($params=array()) {
	// Previous min/max was 2-4% always, ~3000 players, so 60-120 each time.

	$full_max             = (isset($params['full_max']) ? $params['full_max'] : 80); // In: full_max, default 80%
	$minor_revive_to      = (isset($params['minor_revive_to']) ? $params['minor_revive_to'] : 100); // minor_revive_to, default 100
	$major_revive_percent = (isset($params['major_revive_percent']) ? $params['major_revive_percent'] : 5); // major_revive_percent, default 5%
	$just_testing         = (isset($params['just_testing']) ? 'true' : 'false');
	$revive_amount        = 0; // Initial.
	$major_hour           = 3; // Hour for the major revive.

	/* New schedule should be:
	1: revive to 100
	2: revive to 100 (probably 0)
	3: revive 150, (250 total) to a max of 80% of total, ~2500.
	4: revive to 100 (almost certainly no more)
	5: revive to 100 (almost certainly no more)
	6: revive 150, (400 total) to a max of 80% of total, ~2500
	7: ...etc.
	*/

	// Determine the total dead (& confirmed).
	$sel_dead = DatabaseConnection::$pdo->query('select count(*) from players where health<1 and confirmed=1');
	$dead_count = $sel_dead->fetchColumn();

	// If none dead, return false.
	if (!$dead_count) {
		return array(0, 0);
	}

	// Determine the total confirmed.
	$sel_total_active = DatabaseConnection::$pdo->query('select count(*) from players where confirmed=1');
	$total_active = $sel_total_active->fetchColumn();

	// Calc the total alive.
	$total_alive = ($total_active - $dead_count);

	// Determine major or minor based on the hour.
	$sel_current_time = DatabaseConnection::$pdo->query("SELECT amount from time where time_label='hours'");
	$current_time = $sel_current_time->fetchColumn();
	assert(is_numeric($current_time));

	$major = (($current_time % $major_hour) == 0);

	// If minor, and total_alive is more than minor_revive_to-1, return 0/total.
	if (!$major) { // minor
		if ($total_alive>($minor_revive_to-1)) { // minor_revive_to already met.
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
	//die();
	assert(isset($revive_amount));
	assert(isset($current_time));
	assert(isset($just_testing));
	assert(isset($dead_count));
	assert(isset($major));
	// Actually perform the revive on those integers.
	// Use the order by clause to determine who revives, by time, days and then by level, using the limit set previously.
	//select uname, player_id, level,floor(($major_revive_percent/100)*$total_active) days, resurrection_time from players where confirmed = 1 AND health < 1 ORDER BY abs(8 - resurrection_time) asc, level desc, days asc
	$select = "select player_id from players where confirmed = 1 AND health < 1 ORDER BY abs(".intval($current_time)."
			- resurrection_time) asc, level desc, days asc limit ".$revive_amount;
	$up_revive_players= "UPDATE players
					SET status = 0,
					health =
						CASE WHEN ".$just_testing."
						THEN health
						ELSE
							(
							CASE WHEN _class_id = 4
							THEN (150+(level*3))
							ELSE (100+(level*3)) END
							)
						END
					WHERE
					player_id IN (".$select.")";
	$update = DatabaseConnection::$pdo->query($up_revive_players);
	$truly_revived = $update->rowCount();

	// Return the 'revived/total' actually revived.
	return array($truly_revived, $dead_count);
}
?>
