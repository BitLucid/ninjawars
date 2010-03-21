<?php
//$microtimes = array();
//$microtimes[1] = microtime();
require_once(LIB_ROOT."specific/lib_player_list.php");
require_once(LIB_ROOT."specific/lib_player.php");

$alive      = false;
$private    = false;
$quickstat  = false;
$page_title = "Ninja List";

include SERVER_ROOT."interface/header.php";

DatabaseConnection::getInstance();

// The script for the accordian opening.
//echo "<script type='text/javascript' src='js/player_accordian.js'></script>";
// INIT

//$microtimes[2] = microtime();

// TODO: Bring back the "show/hide dead" toggle, store in session, and make dead things able to be shown again.
// TODO: Make the player.php player profile page accept player_id as a substitute for a string in the url.

$username     = get_username();
$searched     = in('searched');
$hide_setting = (!$searched && SESSION::is_set('hide_dead') ? SESSION::get('hide_dead') : 'dead'); // Defaults to hiding dead via session.
$hide         = ($searched ? 'none' : in('hide', $hide_setting)); // search override > get setting > session setting
$alive_only   = ($hide == 'dead');
$current_rank = in('rank_spot', 0);
$rank_spot    = (is_numeric($current_rank) ? $current_rank : 0);
$page         = in('page', 1); // Page will get changed down below.
$alive_count  = 0;
$record_limit = 20; // *** The number of players that gets shown per page.
$view_type    = in('view_type');
$rank         = get_rank($username);
$dead_count   = DatabaseConnection::$pdo->query("SELECT count(player_id) FROM rankings WHERE alive = false");
$dead_count   = $dead_count->fetchColumn();

/*if ($hide != 'dead') {
	$dead_count = 0; // Set the count of dead rows to zero for later listing.
}*/

$page 		 = in('page', ceil(($rank_spot - $dead_count) / $record_limit));

if (!$searched && $hide_setting != $hide) { SESSION::set('hide_dead', $hide); }


//$microtimes[3] = microtime();

// Display the clear search and create the where clause for searching.
$where_clause = "";
// If a search was made, specify letter or word-based search.
// If unless showing dead, check that health is > 0, or alive = true from the ranking.
// Otherwise, no searching was done, so the score


// Select some players from the ranking.
$queryParams = array();

if ($searched) {
	$view_type = 'searched';

	if (strlen($searched) == 1) {
		$where_clause = "WHERE (uname ilike :param".count($queryParams).")";
		$queryParams[] = $searched.'%';
	} else {
		$where_clause = "WHERE (uname ~* :param".count($queryParams).")";
		$queryParams[] = $searched;
	}
} else {
	$where_clause = "WHERE score >= :param".count($queryParams)." ";
	$queryParams[] = $rank_spot;
}

if ($hide == 'dead') {
	$where_clause .= "AND alive = true";
}

$query_count     = "SELECT count(player_id) FROM rankings ".$where_clause;
$count_statement = DatabaseConnection::$pdo->prepare($query_count);

for ($i = 0;$i < count($queryParams); $i++) {	// *** Reformulate if queryParams gets to be more than 3 or for items
	$count_statement->bindValue(':param'.$i, $queryParams[$i]);
}

$count_statement->execute();
$totalrows = $count_statement->fetchColumn();

//$microtimes[4] = microtime();

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
$sel = "SELECT rank_id, uname, class, level, alive, days, _clan_id AS clan_id, clan_name, player_id
	FROM rankings LEFT JOIN clan_player ON player_id = _player_id LEFT JOIN clan ON clan_id = _clan_id ".$where_clause." ORDER BY rank_id ASC, player_id ASC
	LIMIT $record_limit OFFSET $limitvalue";

$ninja_info = DatabaseConnection::$pdo->prepare($sel);

for ($i = 0;$i < count($queryParams); $i++) {	// *** Reformulate if queryParams gets to be more than 3 or for items
	$ninja_info->bindValue(':param'.$i, $queryParams[$i]);
}

$ninja_info->execute();

//$microtimes[5] = microtime();

ob_start();
display_search_form($hide, $page, $searched, $dead_count);
$search_form = ob_get_contents();
ob_end_clean();
	
//$microtimes[6] = microtime();	

// Display the nav
ob_start();
player_list_nav($page, $hide, $searched, $record_limit, $totalrows, $numofpages);
$player_list_nav = ob_get_contents();
ob_end_clean();

//$microtimes[7] = microtime();

$active_ninja = '';
if (!$searched) { // Will not display active ninja on a search page.
	$active_ninja = render_active(5, $alive_only); // Display the currently active ninjas
}

//$microtimes[8] = microtime();

//$microtimes[9] = microtime();
	
// Render each of the player rows.

$ninja_count = 0;
$player_rows = '';
while ($a_player = $ninja_info->fetch()) {
	$ninja_count++;
	$level_cat = level_category($a_player['level']);
	$parts = array(
		'alive_class'     => ($a_player['alive'] == 1 ? "AliveRow" : "DeadRow")
		, 'odd_or_even'   => ($ninja_count % 2 ? "odd" : "even")
		, 'player_rank'   => $a_player['rank_id']
		, 'player_id'     => $a_player['player_id']
		, 'page'          => $page
		, 'uname'         => $a_player['uname']
		, 'level_cat_css' => $level_cat['css']
		, 'level_cat'     => $level_cat['display']
		, 'level'         => $a_player['level']
		, 'class'         => $a_player['class']
		, 'WEB_ROOT'      => WEB_ROOT
		, 'clan_id'       => $a_player['clan_id']
		, 'clan_name'     => $a_player['clan_name']
		, 'alive'         => ($a_player['alive'] ? "&nbsp;" : "Dead"), // alive/dead display
	);

	$player_rows .= render_template('player_list_row.tpl', $parts);	// *** Very inefficient, consider revising ***
	// Add all the player rows on to a big list of 'em.
}

//$microtimes[10] = microtime();

$parts = get_certain_vars(get_defined_vars());

echo render_template('player_list.tpl', $parts);

//$microtimes[11] = microtime();

include SERVER_ROOT."interface/footer.php";

/*$microtimes[12] = microtime();
$start=true;
foreach($microtimes as $num => $time){
	//echo "<!-- Benchmark times";
	if($start){
		var_dump('start: '.$time);
	} else {
		var_dump("$num: ".($time - either($microtimes[$num-1], 0)));
	}
	//echo "-->";
	$start = false;
}*/
?>
