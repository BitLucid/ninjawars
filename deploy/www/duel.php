<?php
require_once(LIB_ROOT."control/lib_player.php");
$private    = false;
$alive      = false;

if ($error = init($private, $alive)) {
	redirect('list.php');
} else {

$stats          = membership_and_combat_stats();
$vicious_killer = $stats['vicious_killer'];

$duels = query_array("SELECT dueling_log.*, attackers.player_id AS attacker_id, defenders.player_id AS defender_id FROM dueling_log JOIN players AS attackers ON attackers.uname = attacker JOIN players AS defenders ON defender = defenders.uname ORDER BY id DESC LIMIT 500");

display_page(
	'duel.tpl'	// *** Main template ***
	, 'Bath House' // *** Page Title ***
	, get_certain_vars(get_defined_vars(), array('duels')) // *** Page Variables ***
	, array( // *** Page Options ***
		'quickstat' => false
	)
);
}
?>
