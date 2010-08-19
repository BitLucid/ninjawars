<?php
$private    = false;
$alive      = false;
$quickstat  = false;
$page_title = "Enemy List";
require_once(LIB_ROOT."specific/lib_player_list.php");

if (!get_user_id()) {
	header('Location: list_all_players.php');
	exit();
}

include SERVER_ROOT."interface/header.php";

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
		return (int) $e1['level']<=$e2['level'];
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

function render_current_enemies($enemy_list) {
	$enemy_section = '';

	if (!is_array($enemy_list)) {
		return $enemy_section;
	}

	/// TODO - Stop iterating database calls. array_map iterates, expand_enemy_info calls the database
	$enemy_list = array_map('expand_enemy_info', $enemy_list); // Turn id into enemy info.
	uasort($enemy_list, 'compare_enemy_order'); // Resort by health, level.

	foreach ($enemy_list as $loop_enemy_id=>$loop_enemy) { // TODO: Turn this into a template render.
		if ($loop_enemy['confirmed']) {
			$action = ($loop_enemy['health'] > 0 ? 'Attack' : 'View');
			$status_class = ($loop_enemy['health'] > 0 ? '' : 'enemy-dead');
			$enemy_section .= "<li class='$status_class'>
	        <a href='enemies.php?remove_enemy=$loop_enemy_id'><img src='".IMAGE_ROOT."icons/delete.png' alt='remove'></a>
	         $action <a href='player.php?player_id=$loop_enemy_id'>".out($loop_enemy['uname'])."</a>
	          ({$loop_enemy['health']} health)</li>";
		}
	}

	return $enemy_section;
}

function render_recent_attackers() {
	$recent_attackers_section = '';
	$recent_attackers = get_recent_attackers();

	if ($recent_attackers->rowCount() > 0) {
		$recent_attackers_section .= "<h3>You were recently attacked by</h3>
			<ul id='recent-attackers'>";
		foreach($recent_attackers as $l_attacker) {
			$status_class = '';

			if ($l_attacker['health'] < 1) {
				$status_class = 'status-dead';
			}

			$recent_attackers_section .= "<li class='recent-attacker {$status_class}'><a href='player.php?player_id={$l_attacker['send_from']}'>{$l_attacker['uname']}</a></li>";
		}

		$recent_attackers_section .= '</ul>';
	}

	return $recent_attackers_section;
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
}

if (is_numeric($remove_enemy)) {
	remove_enemy($remove_enemy);
	$enemy_list = get_setting('enemy_list'); // Update to new enemy list.
}

if (is_numeric($add_enemy)) {
	add_enemy($add_enemy);
	$enemy_list = get_setting('enemy_list'); // Update to new enemy list.
}

$enemy_section = render_current_enemies($enemy_list);

if (count($enemy_list) > ($enemy_limit - 1)) {
	$max_enemies = true;
}

$recent_attackers_section = render_recent_attackers();

$parts = get_certain_vars(get_defined_vars(), array('found_enemies', 'active_ninjas'));

echo render_template('enemies.tpl', $parts);

include SERVER_ROOT."interface/footer.php";
?>
