<?php
require_once(LIB_ROOT."specific/lib_player_list.php");

$alive      = false;
$private    = false;
$quickstat  = false;
$page_title = "Ninja List";

include SERVER_ROOT."interface/header.php";
// The script for the accordian opening.
//echo "<script type='text/javascript' src='js/player_accordian.js'></script>";
// INIT


// TODO: Bring back the "show/hide dead" toggle, store in session, and make dead things able to be shown again.
// TODO: Make the player.php player profile page accept player_id as a substitute for a string in the url.




$username    = get_username();
$searched    = in('searched');
$previously_hiding = (SESSION::is_set('hide_dead')? SESSION::get('hide_dead') : 'dead'); // Defaults to hiding dead via session.
$hide        = ($searched? 'none' : in('hide', $previously_hiding)); // search override > get setting > session setting
$alive_only  = ($hide == 'dead'? true : false);
$current_rank   = in('rank_spot', 0);
$rank_spot   = (is_numeric($current_rank) ? $current_rank : 0);
$page        = in('page', 1); // Page will get changed down below.
$dead_count  = $sql->QueryItem("SELECT count(player_id) FROM rankings WHERE alive = false");
$alive_count = 0;
$record_limit = 20; // *** The number of players that gets shown per page.
$view_type = in('view_type');
/*if ($hide != 'dead') {
	$dead_count = 0; // Set the count of dead rows to zero for later listing.
}*/
$page 		 = in('page', ceil(($rank_spot - $dead_count) / $record_limit));
if(!$searched) { SESSION::set('hide_dead', $hide); }


// Display the clear search and create the sql search params.
$where_clause = "";
// If a search was made, specify letter or word-based search.
// If unless showing dead, check that health is > 0, or alive = true from the ranking.
// Otherwise, no searching was done, so the score






// Select some players from the ranking.
if ($searched){
	$view_type = 'searched';
	if(strlen($searched) == 1){
		$where_clause = "WHERE (uname ilike '$searched%')";
	} else {
		$where_clause = "WHERE (uname ~* '$searched')";
	}
} else {
	$where_clause = "WHERE score >= $rank_spot ";
}

if($hide == 'dead'){
	$where_clause .= "AND alive = true";
}
$query_count  = "SELECT count(player_id) FROM rankings ".$where_clause;
$totalrows    = $sql->QueryItem($query_count);
$rank         = $sql->QueryItem("SELECT rank_id FROM rankings WHERE uname = '".$username."'");
$rank         = ($rank > 0 ? $rank : 1); // Make rank 



// Determine the current page spot navigated to.
// If searching, use the page between 
// If no specific rank was requested, use the viewer's rank
// If a certain rank was requested, use that rank spot.
// Determine the page, if the dead count is more than the rank spot, default to 1, otherwise use the input page.
// Number of pages = ceil($totalrows / $record_limit);
// limit value = ($page * $record_limit) - $record_limit;
if ($searched > 0) {
	$page = ceil($searched / $record_limit);
} else if ($page == "searched") {
	$page = in('page', 1);
} else {
	if (!$rank_spot) {
		$rank_spot = $rank;
	} else {
		$rank_spot = ($rank_spot > 0 ? $rank_spot : $totalrows + 1);
	}
	if ($page == "") {
		$page       = ($dead_count > $rank_spot ? 1 : $page);
	}
	$page = ($page < 1 ? 1 : $page); // Prevent the page number from going negative.
}
$numofpages = ceil($totalrows / $record_limit);
$limitvalue = ($page * $record_limit) - $record_limit;
// Get the ninja information.
$sel = "SELECT rank_id, uname, class, level, alive, days, clan, player_id
	FROM rankings ".$where_clause."  ORDER BY rank_id ASC, player_id ASC
	LIMIT $record_limit OFFSET $limitvalue";
$sql->Query($sel);
$row = $sql->data;
$ninja_count = $sql->rows;


	ob_start();
	display_search_form($hide, $page, $searched, $dead_count);
	$search_form = ob_get_contents();
	ob_end_clean();

	// Display the nav
	ob_start();
	player_list_nav($page, $hide, $searched, $record_limit, $totalrows, $numofpages);
	$player_list_nav = ob_get_contents();
	ob_end_clean();

	if (!$searched) { // Will not display active ninja on a search page.
		ob_start();
		display_active(5, $alive_only); // Display the currently active ninjas
		$active_ninja = ob_get_contents();
		ob_end_clean();
	} else {
		$active_ninja = '';
	}

	$players = $sql->FetchAll();
	
	// Render each of the player rows.
	$i = 0;
	$player_rows = '';
	foreach($players as $a_player){
		$i++;
		$level_cat = level_category($a_player['level']);
		$parts = array(
			'alive_class' => ($a_player['alive'] == 1? "AliveRow" : "DeadRow"),
			'odd_or_even' => ($i % 2 ? "odd" : "even"),
			'player_rank' => $a_player['rank_id'],
			'player_id' => $a_player['player_id'],
			'page' => $page,
			'uname' => $a_player['uname'],
			'level_cat_css' => $level_cat['css'],
			'level_cat' => $level_cat['display'],
			'level' => $a_player['level'],
			'class' => $a_player['class'],
			'WEB_ROOT' => WEB_ROOT,
			'clan' => $a_player['clan'],
			'alive' => ($a_player['alive']? "&nbsp;" : "Dead"), // alive/dead display
		);
		$player_rows .= render_template('player_list_row.tpl', $parts);
		// Add all the player rows on to a big list of 'em.
	}

// Main display section.
$parts = array(
	'searched' => $searched,
	'ninja_count' => $ninja_count,
	'search_form' => $search_form,
	'player_list_nav' => $player_list_nav,
	'active_ninja' => $active_ninja,
	'player_rows' => $player_rows, // Display the concatenated player rows.
	'hide' => $hide,
	'WEB_ROOT' => WEB_ROOT,
);
echo render_template('player_list.tpl', $parts);

include SERVER_ROOT."interface/footer.php";
?>
