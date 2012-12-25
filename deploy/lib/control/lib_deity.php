<?php
/*
 * Functions for use in the deities.
 *
 * @package deity
 * @subpackage deity_lib
 */
 

// Determine the score for ranking.
function get_score_formula(){
	$score = '(level*1000 + gold/100 + kills*3 - days*5)';
	return $score;
}

function delete_old_messages() {
    $statement = query("delete from messages where date < ( now() - '3 months'::interval)");
	return $statement->rowCount();
}

function delete_old_events() {
    $statement = query("delete from events where date < ( now() - '4 days'::interval)");
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
	$deleted = DatabaseConnection::$pdo->prepare("DELETE FROM chat WHERE chat_id NOT IN (SELECT chat_id FROM chat ORDER BY date DESC LIMIT :msg_limit)");  //Deletes old chat messages.
	$deleted->bindValue(':msg_limit', $message_limit);
	$deleted->execute();
	return (int) $deleted->rowCount();
}

// This actually toggles the "active" column on players, not the confirm column, and if they log in again, they're instantly active again.
function unconfirm_older_players_over_minimums($keep_players=2300, $unconfirm_days_over=90, $max_to_unconfirm=30, $just_testing=true) {
	$change_confirm_to = ($just_testing ? '1' : '0'); // Only unconfirm players when not testing.
	$minimum_days = 30;
	$max_to_unconfirm = (is_numeric($max_to_unconfirm) ? $max_to_unconfirm : 30);
	$players_unconfirmed = null;
	DatabaseConnection::getInstance();
	$sel_cur = DatabaseConnection::$pdo->query("SELECT count(*) FROM players WHERE active = 1");
	$current_players = $sel_cur->fetchColumn();

	if ($current_players < $keep_players) {
		// *** If we're under the minimum, don't inactivate anyone.
		return false;
	}

	// *** Don't unconfirm anyone below the minimum floor.
	$unconfirm_days_over = max($unconfirm_days_over, $minimum_days);

	// Unconfirm at a maximum of 20 players at a time.
	$unconfirm_within_limits = "UPDATE players
		SET active = :active
		WHERE players.player_id
		IN (
			SELECT player_id FROM players
			WHERE active = 1
			AND days > :age
			ORDER BY player_id DESC	LIMIT :max)";
	$update = DatabaseConnection::$pdo->prepare($unconfirm_within_limits);
	$update->bindValue(':active', $change_confirm_to);
	$update->bindValue(':age', intval($unconfirm_days_over));
	$update->bindValue(':max', $max_to_unconfirm);
	$update->execute();
	return $update->rowCount();
}

// Take all characters, and heal them one step closer to their maximum base.
function heal_characters($basic=8, $with_level=true, $maximum_heal='200'){
	/*
	Goal:  Faster regen for higher level.
	See the balance sheet: 
	https://docs.google.com/spreadsheet/ccc?pli=1&key=0AkoUgtBBP00HdGs0Tmk4bC10TXN0SUJYXzdYMVpFZFE#gid=0
	*/


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
 * @params array('full_max'=>80, 'minor_revive_to'=>100, 'major_revive_percent'=>5,
 *      'just_testing'=>false)
**/
function revive_players($params=array()) {
	// Previous min/max was 2-4% always, ~3000 players, so 60-120 each time.

	$full_max             = (isset($params['full_max']) ? $params['full_max'] : 80); // In: full_max, default 80%
	$minor_revive_to      = (isset($params['minor_revive_to']) ? $params['minor_revive_to'] : 100); // minor_revive_to, default 100
	$major_revive_percent = (isset($params['major_revive_percent']) ? $params['major_revive_percent'] : 5); // major_revive_percent, default 5%
	$just_testing         = isset($params['just_testing']);
	$revive_amount        = 0; // Initial.
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
	//die();
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

// Update the vicious killer stat.
function update_most_vicious_killer_stat() {
	$vk = DatabaseConnection::$pdo->query('SELECT uname FROM levelling_log JOIN players ON player_id = _player_id WHERE killsdate = cast(now() AS date) GROUP BY uname, killpoints ORDER BY killpoints DESC LIMIT 1');
	$todaysViciousKiller = $vk->fetchColumn();
	if ($todaysViciousKiller) {
		$update = DatabaseConnection::$pdo->prepare('UPDATE past_stats SET stat_result = :visciousKiller WHERE id = 4'); // 4 is the ID of the vicious killer stat.
		$update->bindValue(':visciousKiller', $todaysViciousKiller);
		$update->execute();	
	}
}

?>
