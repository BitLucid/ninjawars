<?php
/*
 * Functions for use in the deities.
 * 
 * @package deity
 * @subpackage deity_lib
 */

require_once(substr(__FILE__,0,(strpos(__FILE__, 'lib/')))."lib/base.inc.php"); // *** Absolute path include of everything.

// Name the chat time will send under.
define('CHAT_TIME_NAME', '----');

function update_days($sql){
	$sql->Update("UPDATE players SET days = days+1");
	$players = $sql->a_rows;
	return $players;
}

// Chats stuff.
function shorten_chat($sql, $message_limit=800){
    // Find the latest 800 messages and delete all the rest;
	$deleted = $sql->Update("delete from chat where id not in (select id from chat order by time desc limit ".$message_limit.")");  //Deletes old chat messages.
	return (int) $deleted;
}

// Chat time-if-no-one-chatted-recently message
function chat_timer(){
    $sender = CHAT_TIME_NAME;
    $sql = new DBAccess();
    $last_chat = $sql->QueryItem('select send_from from chat order by id desc limit 1');
    if ($last_chat != $sender){
        sendChat($sender,"ChatMsg", "----".date("h:i")."----");
    }
}


function unconfirm_older_players_over_minimums($keep_players=2300, $unconfirm_days_over=90, $max_to_unconfirm=30, $just_testing=true){
	$change_confirm_to = ($just_testing? '1' : '0'); // Only unconfirm players when not testing.
	$minimum_days = 30;
	$max_to_unconfirm = (is_numeric($max_to_unconfirm) ? $max_to_unconfirm : 30 );
	$players_unconfirmed = null;
	$db = new DBAccess();
	$sel_cur = "select count(*) from players where confirmed = 1";
	$current_players = $db->QueryItem($sel_cur);
	if ($current_players<$keep_players){
		// *** If we're under the minimum, don't unconfirm anyone.
		return false;
	}
	if (intval($unconfirm_days_over)<$minimum_days){
		// *** Don't unconfirm anyone below the minimum floor.
		$unconfirm_days_over = $minimum_days;
	}
	// Unconfirm at a maximum of 20 players at a time.
	$unconfirm_within_limits = "update players 
		set confirmed = ".$change_confirm_to."
		where players.player_id 
		IN ( 
			select player_id from players 
			where confirmed = 1 
			and days > ".intval($unconfirm_days_over)." 
			order by player_id DESC	limit ".$max_to_unconfirm."
			)";
	$db->Update($unconfirm_within_limits);
	$players_unconfirmed = $db->getAffectedRows();
	return $players_unconfirmed;
}

// Test that the unconfirming never oversteps its bounds and unconfirms more than it should.
function test_unconfirm_older_players_over_minimums(){
	if (DEBUG) {
		$original_error_reporting = error_reporting();
		error_reporting(E_ALL | E_STRICT); //error_reporting(E_ALL); 
		// Turns on strict standards and notices.
	}
	
	// These run dummy unconfirmations.
	
	$unconfirmed = unconfirm_older_players_over_minimums(2300, 90);
	assert($unconfirmed < 21);
	// IN: Default limits, out: nothing more than the max unconfirmed.
	
	$unconfirmed = unconfirm_older_players_over_minimums(1, 1);
	assert($unconfirmed < 30);
	// in: when attempting to unconfirm everyone... out: a failsafe max of 30 are affected
	
	$unconfirmed = unconfirm_older_players_over_minimums(1, 500);
	assert($unconfirmed == 0);
	// in: something that should match no-one, out: 0 unconfirmed
	
	$unconfirmed = unconfirm_older_players_over_minimums(10000, 1);
	assert($unconfirmed == 0);
	// in: something that should match no-one, out: 0 unconfirmed
	
	
	if (DEBUG) {
		error_reporting($original_error_reporting);
	}
}

/**
 * Revive up to a small max in minor hours, and a stable percent on major hours.
 * Defaults
 * sample_use: revive_players(array('just_testing'=>true));
 * @params array('full_max'=>80, 'minor_revive_to'=>100, 'major_revive_percent'=>5,
 *      'just_testing'=>false)
**/
function revive_players($params=array()){
    // Previous min/max was 2-4% always, ~3000 players, so 60-120 each time.
    // In: full_max, default 80%
    $full_max = isset($params['full_max'])? $params['full_max'] : 80;
    // minor_revive_to, default 100
    $minor_revive_to = isset($params['minor_revive_to'])? $params['minor_revive_to'] : 100;
    // major_revive_percent, default 5%
    $major_revive_percent = isset($params['major_revive_percent'])? $params['major_revive_percent'] : 5;
    $just_testing = isset($params['just_testing'])? 'true' : 'false';
    $major_hour = 3; // Hour for the major revive.
    $revive_amount = 0; // Initial.
        
    /* New schedule should be:
    1: revive to 100
    2: revive to 100 (probably 0)
    3: revive 150, (250 total) to a max of 80% of total, ~2500.
    4: revive to 100 (almost certainly no more)
    5: revive to 100 (almost certainly no more)
    6: revive 150, (400 total) to a max of 80% of total, ~2500
    7: ...etc.
    */
    
    // SQL pulls.
    $db = new DBAccess();
    // Determine the total dead (& confirmed).
    $sel_dead = 'select count(*) from players where health<1 and confirmed=1';
    $dead_count = $db->QueryItem($sel_dead);
    // If none dead, return false.
    if(!$dead_count){
        return array(0, 0);
    }
    // Determine the total confirmed.
    $sel_total_active = 'select count(*) from players where confirmed=1';
    $total_active = $db->QueryItem($sel_total_active);
    // Calc the total alive.
    $total_alive = ($total_active - $dead_count);
    // Determine major or minor based on the hour.
    $sel_current_time = "SELECT amount from time where time_label='hours'";
	$current_time = $db->QueryItem($sel_current_time);
	assert(is_numeric($current_time));
	$major = false;
	if(($current_time % $major_hour) == 0){
	    $major = true;
	}
    // If minor, and total_alive is more than minor_revive_to-1, return 0/total.
    if(!$major){ // minor
        if($total_alive>($minor_revive_to-1)){ // minor_revive_to already met. 
            return array(0, $dead_count);
        } else {  // else revive minor_revive_to - total_alive.
            $revive_amount = floor($minor_revive_to - $total_alive);
        }
    } else { // major.
        $percent_int = floor(($major_revive_percent/100)*$total_active);
        if($dead_count<$percent_int){
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
                    		CASE WHEN class='White' 
                    		THEN (150+(level*3)) 
                    		ELSE (100+(level*3)) END
                    		)
                    	END
                    WHERE
                    player_id IN (".$select.")
                  ";
	$db->Update($up_revive_players);
	$truly_revived = $db->getAffectedRows();
    // Return the 'revived/total' actually revived.
    return array($truly_revived, $dead_count);
}

/*
// Revive the appropriate amount of players
function revive_appropriate_players($min=null, $max=null, $by_percent=false, $just_testing=true){
	$info = array('min'=>null, 'max'=>null, 'total_active'=>null, 'target_number'=>null, 'revived'=>null);
	if ($min==null){
		$min=0;
	}
	if ($max===null){
		$max=100;
	}
	$just_testing = ($just_testing? 'true' : 'false'); // Makes the just testing sql compatible.
	
	$db = new dbAccess();
	$sel_revive_targets = 'select count(*) from players where health<1 and confirmed=1';
	// get number of potential targets.
	$target_number = $db->QueryItem($sel_revive_targets);
	$info['target_number'] = $target_number;
	// Test whether the max is zero or there are no targets.
	$revived = false;
	$info['revived']=$revived;
	if ($max == 0 || $target_number == 0){
		return $info;
	}	
	$sel_total_active = 'select count(*) from players where confirmed=1';
	$total_active = $db->QueryItem($sel_total_active);
	$info['total_active'] = $total_active;
	// Get the precise number via percentage or else just the number.
	$limit = $total_active;
	if ($by_percent){ // Turn the min and max percentages into whole player numbers.
		$max = ceil($total_active * ($max/100));
		$min = floor($total_active * ($min/100));
	}
	$limit = ($limit>$max?$max : $limit);
	$limit = ($limit<$min?$min : $limit);
	assert($limit>=$min && ($limit<=$max || $max<$min));

	$info['max']=$max;
	$info['min']=$min;
	
	$sel_current_time = "SELECT amount from time where time_label='hours'";
	$current_time = $db->QueryItem($sel_current_time);
	assert(is_numeric($current_time));
	
	// Use the order by clause to determine who revives, by time, days and then by level, using the limit set previously.
	//select uname, player_id, level, days, resurrection_time from players where confirmed = 1 AND health < 1 ORDER BY abs(8 - resurrection_time) asc, level desc, days asc
	$select = "select player_id from players where confirmed = 1 
	        AND health < 1 ORDER BY abs(".intval($current_time)." - resurrection_time) asc, level desc, days asc 
	        limit ".$limit;
	$up_revive_players= "UPDATE players 
                    SET status = 0, 
                    health = 
                    	CASE WHEN ".$just_testing." 
                    	THEN health
                    	ELSE 
                    		(
                    		CASE WHEN class='White' 
                    		THEN (150+(level*3)) 
                    		ELSE (100+(level*3)) END
                    		)
                    	END
                    WHERE
                    player_id IN (".$select.")
                  ";
	$db->Update($up_revive_players);
	$info['revived'] = $db->getAffectedRows();
	if (DEBUG) {
		//var_dump('REVIVED', $select, $info);
		//print_r($up_revive_players);
	}
	// Return the number revived.
	return $info;
	// TODO: This should stop reviving players when enough are revived?  Eh, meaningless, I suppose.
}
*/

// Test the player revival system
function test_revive_appropriate_players(){
	if (DEBUG) {
		$original_error_reporting = error_reporting();
		error_reporting(E_ALL | E_STRICT); //error_reporting(E_ALL); 
		// Turns on strict standards and notices.
	}
	
	/*$minimum = 0;
	$by_percent = false;
	$maximum = 0;
	$info = revive_appropriate_players($minimum, $maximum, $by_percent, $just_testing=true);
	assert($info['revived'] == false);
	
	$minimum = 10;
	$by_percent = false;
	$maximum = 1;
	$info = revive_appropriate_players($minimum, $maximum, $by_percent, $just_testing=true);
	assert($info['revived'] == 10);
	
	$minimum = 10;
	$by_percent = false;
	$maximum = 10;
	$revived = revive_appropriate_players($minimum, $maximum, $by_percent, $just_testing=true);
	assert($info['revived'] == 10);
	
	$minimum = 2;
	$by_percent = false;
	$maximum = 4;
	$info = revive_appropriate_players($minimum, $maximum, $by_percent);
	assert($info['revived'] > 1 && $info['revived'] < 5);
	
	$minimum = 4;
	$by_percent = false;
	$maximum = null;
	$info = revive_appropriate_players($minimum, $maximum, $by_percent);
	assert($info['revived'] > 3);
	
	$minimum = 3;
	$by_percent = true;
	$maximum = 10;
	$info = revive_appropriate_players($minimum, $maximum, $by_percent);
	assert($info['revived']<$info['target_number']);
	
	$minimum = 100;
	$by_percent = true;
	$maximum = 100;
	$info = revive_appropriate_players($minimum, $maximum, $by_percent);
	assert($info['revived']==$info['target_number']);
	
	$minimum = 0;
	$by_percent = true;
	$maximum = 100;
	$info = revive_appropriate_players($minimum, $maximum, $by_percent);
	assert($info['revived'] == $info['target_number']);
	
	$minimum = 3;
	$by_percent = true;
	$maximum = 10;
	$info = revive_appropriate_players($minimum, $maximum, $by_percent);
	assert($info['revived']<$info['target_number']);
	*/
	
	if (DEBUG) {
		error_reporting($original_error_reporting);
	}
}

if (DEBUG) {
	test_unconfirm_older_players_over_minimums();
	test_revive_appropriate_players();
}


?>
