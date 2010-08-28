<?php
$private    = true;
$alive      = false;
require_once(LIB_ROOT."specific/lib_player_list.php");

if ($error = init($private, $alive)) {
	header('Location: list_all_players.php');
} else {

function get_enemy_matches($match_string) {
	DatabaseConnection::getInstance();

	$user_id = get_user_id();

	$statement = DatabaseConnection::$pdo->prepare("SELECT player_id, uname FROM players WHERE uname ~* :matchString AND confirmed = 1 AND player_id != :user ORDER BY level LIMIT 11");
	$statement->bindValue(':matchString', $match_string);
	$statement->bindValue(':user', $user_id);

	$statement->execute();

	return $statement;
}

function add_enemy($enemy_id) {
	if (!is_numeric($enemy_id)) {
		throw new Exception('Enemy id to add must be present to succeed.');
	}

	$enemy_list = get_setting('enemy_list');
	$enemy_list[$enemy_id] = $enemy_id;
	set_setting('enemy_list', $enemy_list);
}

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

function expand_enemy_info($enemy_id) {
	$enemy = get_player_info($enemy_id);
	$enemy['enemy_id'] = $enemy_id;
	return $enemy;
}

function get_current_enemies($enemy_list) {
	if (!is_array($enemy_list)) {
		return $enemy_section;
	}

	/// TODO - Stop iterating database calls. array_map iterates, expand_enemy_info calls the database
	$enemy_list = array_map('expand_enemy_info', $enemy_list); // Turn id into enemy info.
	uasort($enemy_list, 'compare_enemy_order'); // Resort by health, level.

	return $enemy_list;
}

function get_recent_attackers() {
	$recent_attackers = array();
	$user_id = get_user_id();
	DatabaseConnection::getInstance();

	$statement = DatabaseConnection::$pdo->prepare('SELECT DISTINCT send_from, uname, level, health FROM events JOIN players ON send_from = player_id WHERE send_to = :user LIMIT 20');
	$statement->bindValue(':user', $user_id);
	$statement->execute();

	return $statement;
}


$active_ninjas = get_active_players(5, true); // Get the currently active ninjas

$match_string = in('enemy_match', null, 'no filter');
$add_enemy    = in('add_enemy', null, 'toInt');
$remove_enemy = in('remove_enemy', null, 'toInt');
$enemy_limit  = 20;
$max_enemies  = false;
$enemy_list   = get_setting('enemy_list');

if ($match_string) {
	$found_enemies = get_enemy_matches($match_string);
	$found_enemies = $found_enemies->fetchAll();
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
	, get_certain_vars(get_defined_vars(), array('found_enemies', 'active_ninjas', 'recent_attackers', 'enemy_list')) // *** Page Variables ***
	, array( // *** Page Options ***
		'quickstat' => false
	)
);
}
?>
