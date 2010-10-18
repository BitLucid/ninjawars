<?php
require_once(LIB_ROOT."specific/lib_player_list.php");

// Search for enemies to add.
function get_enemy_matches($match_string) {
	$user_id = get_user_id();
	$sel = "SELECT player_id, uname FROM players WHERE uname ~* :matchString AND active = 1 AND player_id != :user ORDER BY level LIMIT 11";
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

	$enemy_list = get_setting('enemy_list');
	$enemy_list[$enemy_id] = $enemy_id;
	set_setting('enemy_list', $enemy_list);
}

// Drop a certain enemy from the list.
function remove_enemy($enemy_id) {
	if (!is_numeric($enemy_id)) {
		throw new Exception('Enemy id to remove must be present to succeed.');
	}

	$enemy_list = get_setting('enemy_list');

	if (isset($enemy_list[$enemy_id])) {
		unset($enemy_list[$enemy_id]);
	}

	set_setting('enemy_list', $enemy_list);
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
	$enemy = get_player_info($enemy_id);
	$enemy = format_health_percent($enemy);
	$enemy['enemy_id'] = $enemy_id;
	return $enemy;
}


// Pull the current enemies, expand out their info, and then sort 'em by health & level.
function get_current_enemies($enemy_list) {
	if (!is_array($enemy_list)) {
		return $enemy_list;
	}

	/// TODO - Stop iterating database calls. array_map iterates, expand_enemy_info calls the database
	$enemy_list = array_map('expand_enemy_info', $enemy_list); // Turn id into enemy info.
	uasort($enemy_list, 'compare_enemy_order'); // Resort by health, level.

	return $enemy_list;
}

// Pull the recent attackers from the event table.
function get_recent_attackers() {
	$recent_attackers = array();
	$user_id = get_user_id();
	DatabaseConnection::getInstance();

	$statement = DatabaseConnection::$pdo->prepare(
		'SELECT DISTINCT player_id, send_from, uname, level, health 
		FROM events JOIN players ON send_from = player_id WHERE send_to = :user LIMIT 20');
	$statement->bindValue(':user', $user_id);
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

	$peers = array_map('format_health_percent', $peers);
	return $peers;
}

$private    = true;
$alive      = false;

if ($error = init($private, $alive)) {
	header('Location: list.php');
} else {

$char_name = get_char_name();

$peers = nearby_peers(get_char_id());

$active_ninjas = get_active_players(5, true); // Get the currently active ninjas

$char_info = get_player_info();

$match_string = in('enemy_match', null, 'no filter');
$add_enemy    = in('add_enemy', null, 'toInt');
$remove_enemy = in('remove_enemy', null, 'toInt');
$enemy_limit  = 20;
$max_enemies  = false;
$enemy_list   = get_setting('enemy_list');

if ($match_string) {
	$found_enemies = get_enemy_matches($match_string);
} else {
	$found_enemies = null;
}

if (is_numeric($remove_enemy)) {
	remove_enemy($remove_enemy);
	$enemy_list = get_setting('enemy_list'); // Update to new enemy list.
}

if (is_numeric($add_enemy)) {
	add_enemy($add_enemy);
	$enemy_list = get_setting('enemy_list'); // Update to new enemy list.
}

if (count($enemy_list) > ($enemy_limit - 1)) {
	$max_enemies = true;
}

$enemy_list = get_current_enemies($enemy_list);
$recent_attackers = get_recent_attackers()->fetchAll();

display_page(
	'enemies.tpl'	// *** Main template ***
	, 'Enemy List' // *** Page Title ***
	, get_certain_vars(get_defined_vars(), array('char_name', 'char_info', 'found_enemies', 'active_ninjas', 'recent_attackers', 'enemy_list', 'peers')) // *** Page Variables ***
	, array( // *** Page Options ***
		'quickstat' => false
	)
);
}
?>
