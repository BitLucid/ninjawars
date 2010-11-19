<?php

// TODO: Oh god, these functions need templates.

/**
 * This determines how the clans get ranked and tagged, and how to only show non-empty clans.
**/
function player_size() {
	$res = array();
	DatabaseConnection::getInstance();
	$sel = "SELECT (level-3-round(days/5)) AS sum, player_id, uname FROM players WHERE active = 1 AND health > 0 ORDER BY sum DESC";
	$statement = DatabaseConnection::$pdo->query($sel);

	$player_info = $statement->fetch();

	$max = $player_info['sum'];

	do {
		// make percentage of highest, multiply by 10 and round to give a 1-10 size
		$res[$player_info['uname']] = array(
			'player_id'=>$player_info['player_id'],
	      	'size'=> floor(( (($player_info['sum']-1 < 1 ? 0 : $player_info['sum']-1)) /$max)*10)+1);
	} while ($player_info = $statement->fetch());

	return $res;
}

// query the recently active players
function get_active_players($limit=5, $alive_only=true) {
	$where_cond = ($alive_only ? ' AND health > 0' : '');
	$sel = "SELECT uname, player_id FROM players WHERE active = 1 $where_cond ORDER BY last_started_attack DESC LIMIT :limit";
	$active_ninjas = query_array($sel, array(':limit'=>array($limit, PDO::PARAM_INT)));
	return $active_ninjas;
}

// Function to format each row of the player list.
function format_ninja_row($a_player){
	$level_cat = level_category($a_player['level']);
	$row = array(
		'alive_class'     => ($a_player['alive'] == 1 ? "AliveRow" : "DeadRow")
		, 'player_rank'   => $a_player['rank_id']
		, 'player_id'     => $a_player['player_id']
		, 'uname'         => $a_player['uname']
		, 'level_cat_css' => $level_cat['css']
		, 'level_cat'     => $level_cat['display']
		, 'level'         => $a_player['level']
		, 'class'         => $a_player['class']
		, 'class_theme'   => $a_player['class_theme']
		, 'class_identity'=> $a_player['class_identity']
		, 'clan_id'       => $a_player['clan_id']
		, 'clan_name'     => $a_player['clan_name']
		, 'alive'         => ($a_player['alive'] ? "&nbsp;" : "Dead"), // alive/dead display
	);
	return $row;
}

// Consolidated from lib_game.php to here.


function getMemberCount() {
	DatabaseConnection::getInstance();

	$statement = DatabaseConnection::$pdo->query("SELECT count(session_id) FROM ppl_online WHERE member AND activity > (now() - CAST('30 minutes' AS interval)) UNION SELECT count(session_id) FROM ppl_online WHERE member");
	$members = $statement->fetchColumn();
	$membersTotal = $statement->fetchColumn();

	return array('active'=>$members, 'total'=>$membersTotal);
}

?>
