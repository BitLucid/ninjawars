<?php 
// Ninja grouping functionalities, including enemies/friends, eventually groups, node listings, etc.


// Search for enemies to add.
function get_enemy_matches($match_string) {
	$user_id = self_char_id();
	// Doesn't really cause any problems to allow like match characters to pass through here.
	$sel = "SELECT player_id, uname FROM players 
		WHERE uname ilike :matchString || '%' AND active = 1 AND player_id != :user 
		ORDER BY level LIMIT 11";
	$enemies = query_array($sel, array(
		':matchString' => $match_string
		, ':user' => $user_id
		)
	);

	return $enemies;
}

// Add a certain enemy to the enemy list.
function add_enemy($enemy_id) {
	if (!is_numeric($enemy_id)) {
		throw new Exception('Enemy id to add must be present to succeed.');
	}
	remove_enemy($enemy_id);

	DatabaseConnection::getInstance();
	$query = 'INSERT INTO enemies (_player_id, _enemy_id) VALUES (:pid, :eid)';
	$statement = DatabaseConnection::$pdo->prepare($query);
	$statement->bindValue(':pid', self_char_id());
	$statement->bindValue(':eid', $enemy_id);
	$statement->execute();
}

// Drop a certain enemy from the list.
function remove_enemy($enemy_id) {
	if (!is_numeric($enemy_id)) {
		throw new Exception('Enemy id to remove must be present to succeed.');
	}

	DatabaseConnection::getInstance();
	$query = 'DELETE FROM enemies WHERE _player_id = :pid AND _enemy_id = :eid';
	$statement = DatabaseConnection::$pdo->prepare($query);
	$statement->bindValue(':pid', self_char_id());
	$statement->bindValue(':eid', $enemy_id);
	$statement->execute();
}

/**
 * Comparison sort of two enemies by health, level.
**/
function compare_enemy_order($e1, $e2) {
	if ($e1['health'] == $e2['health']) {
		return (int) $e1['level'] <= $e2['level'];
	} elseif ($e1['health'] >= $e2['health']) {
		return -1;
	} else {
		return 1;
	}
}

// Get the info for a certain single enemy.
function expand_enemy_info($enemy_id) {
	$enemy = char_info($enemy_id);
	$enemy = format_health_percent($enemy);
	$enemy['enemy_id'] = $enemy_id;
	return $enemy;
}


// Pull the current enemies, expand out their info, and then sort 'em by health & level.
function get_current_enemies() {
	$query = 'SELECT player_id, active, level, uname, health, least(100,floor((health / (150 + ((level-1)*25))::float)*100)) 
		AS health_percent FROM players JOIN enemies ON _enemy_id = player_id AND _player_id = :pid 
		WHERE active = 1 ORDER BY health DESC, level DESC';
	return query_resultset($query, [':pid'=>self_char_id()]);
}

// Pull the recent attackers from the event table.
function get_recent_attackers($self=null) {
	// If a self Player object is passed in, exclude them from the "recent attackers" list
	$id = $self instanceof Player? $self->id() : null;

	$exclude_id = '';
	if($id>0){
		$exclude_id = ' AND player_id != :id ';
	}
	DatabaseConnection::getInstance();
	$statement = DatabaseConnection::$pdo->prepare(
		'SELECT DISTINCT player_id, send_from, uname, level, health 
		FROM events JOIN players ON send_from = player_id WHERE send_to = :user AND active = 1 '.$exclude_id.' LIMIT 20');
	$statement->bindValue(':user', self_char_id());
	if($id>0){
		$statement->bindValue(':id', $id, PDO::PARAM_INT);
	}
	$statement->execute();

	return $statement;
}

// Select characters right nearby in ranking score, up and down.
function nearby_peers($char_id/*, $limit=5*/) {
	$sel =
		"(SELECT rank_id, uname, level, player_id, health FROM players JOIN player_rank ON _player_id = player_id WHERE score >
            (SELECT score FROM player_rank WHERE _player_id = :char_id) AND active = 1 AND health > 0 ORDER BY score ASC LIMIT 5)
        UNION
        (SELECT rank_id, uname, level, player_id, health FROM players JOIN player_rank ON _player_id = player_id WHERE score <
            (SELECT score FROM player_rank WHERE _player_id = :char_id2) AND active = 1 AND health > 0 ORDER BY score DESC LIMIT 5)";
	$peers = query_array($sel, array(
		':char_id'=>array($char_id, PDO::PARAM_INT)
		, ':char_id2'=>array($char_id, PDO::PARAM_INT)
		/*, ':limit'=>array($limit, PDO::PARAM_INT)
		, ':limit2'=>array($limit, PDO::PARAM_INT)*/
		)
	);
	if(!count($peers)){
		// Get bottom 10 players if not yet ranked.
		$peers = query_array('SELECT rank_id, uname, level, player_id, health FROM players JOIN player_rank ON _player_id = player_id 
			where active = 1 and health > 0
			order by rank_id desc limit 10');	
	}

	$peers = array_map('format_health_percent', $peers);
	return $peers;
}
