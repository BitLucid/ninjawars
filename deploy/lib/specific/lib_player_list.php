<?php

// TODO: Oh god, these functions need templates.

/**
 * This determines how the clans get ranked and tagged, and how to only show non-empty clans.
**/
function player_size() {
	$res = array();
	DatabaseConnection::getInstance();
	$sel = "SELECT (level-3-round(days/5)) AS sum, player_id, uname FROM players WHERE confirmed = 1 AND health > 0 ORDER BY sum DESC";
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

function render_player_tags() {
	$players = player_size();
	//$clans = @natsort2d($clans, 'level');
	$res = "<div id='player-tags'>
	              <h4 id='player-tags-title'>
	                  All Players
	              </h4>
	          <ul>";

	foreach ($players as $player => $info) {
		$res .= "<li class='player-tag size{$info['size']}'>
	              <a href='player.php?player_id=".urlencode($info['player_id'])."'>$player</a>
	          </li>";
	}

	$res .= "</ul>
	          </div>";

	return $res;
}

// Display the recently active players
function render_active($limit=5, $alive_only=true) {
	$where_cond = ($alive_only ? 'and health>0' : '');
	$sel = "select uname, player_id from players where confirmed=1 $where_cond order by last_started_attack desc limit :limit";
	$active_ninjas = query_array($sel, array(':limit'=>array($limit, PDO::PARAM_INT)));
	$active = render_template('player_list.active.tpl', array('active_ninjas'=>$active_ninjas));
	return $active;
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


// Display first/previous/page/next/last
function render_player_list_nav($page, $hide, $searched, $record_limit, $totalrows, $numofpages) {

	$pageprev = $page -1;
	$pagenext = $page +1;
    $last_page = (($totalrows - ($record_limit * $page)) > 0);
	
	// Use the page's template, here.
	$nav = render_template('player_list.nav.tpl', 
	    array(
	        'page'=>$page,
	        'hide'=>$hide,
	        'searched'=>$searched,
	        'record_limit'=>$record_limit,
	        'totalrows'=>$totalrows,
	        'numofpages'=>$numofpages,
	        'pageprev'=>$pageprev,
	        'pagenext'=>$pagenext,
            'last_page'=>$last_page));
    return $nav;
} // End of display functions.


?>
