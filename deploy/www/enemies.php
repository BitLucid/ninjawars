<?php
require_once(LIB_ROOT."control/lib_player_list.php");
require_once(LIB_ROOT."control/lib_grouping.php");


$private    = true;
$alive      = false;

if ($error = init($private, $alive)) {
	header('Location: list.php');
} else {

$char_name = self_name();

$peers = nearby_peers(self_char_id());

$active_ninjas = get_active_players(5, true); // Get the currently active ninjas

$char_info = self_info();

$match_string = in('enemy_match', null, 'no filter');
$add_enemy    = in('add_enemy', null, 'toInt');
$remove_enemy = in('remove_enemy', null, 'toInt');
$enemy_limit  = 20;
$max_enemies  = false;
$enemy_list = null;


if ($match_string) {
	$found_enemies = get_enemy_matches($match_string);
} else {
	$found_enemies = null;
}

if (is_numeric($remove_enemy) && $remove_enemy != 0) {
	remove_enemy($remove_enemy);
}

if (is_numeric($add_enemy) && $add_enemy != 0) {
	add_enemy($add_enemy);
}

if (count($enemy_list) > ($enemy_limit - 1)) {
	$max_enemies = true;
}

$enemy_list = get_current_enemies();
$enemyCount = $enemy_list->rowCount();
$enemy_list = $enemy_list->fetchAll();
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
