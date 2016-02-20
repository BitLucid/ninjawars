<?php
use NinjaWars\core\data\DatabaseConnection;

// query the recently active players
function get_active_players($limit=5, $alive_only=true) {
	$where_cond = ($alive_only ? ' AND health > 0' : '');
	$sel = "SELECT uname, player_id FROM players WHERE active = 1 $where_cond ORDER BY last_started_attack DESC LIMIT :limit";
	$active_ninjas = query_array($sel, array(':limit'=>array($limit, PDO::PARAM_INT)));
	return $active_ninjas;
}

// Pull an array of different activity counts.
function member_counts() {
	$counts = query_array("(SELECT count(session_id) FROM ppl_online WHERE member AND activity > (now() - CAST('30 minutes' AS interval))) 
		UNION ALL (SELECT count(session_id) FROM ppl_online WHERE member) 
		UNION ALL (select count(player_id) from players where active = 1)");
	$active_row = array_shift($counts);
	$online_row = array_shift($counts);
	$total_row = array_shift($counts);
	return array('active'=>reset($active_row), 'online'=>reset($online_row), 'total'=>end($total_row));
}

