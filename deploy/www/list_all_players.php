<?php
require_once(LIB_ROOT."specific/lib_player_list.php");
require_once(LIB_ROOT."specific/lib_player.php");

$alive      = false;
$private    = false;
$quickstat  = false;
$page_title = "Ninja List";

include SERVER_ROOT."interface/header.php";

DatabaseConnection::getInstance();


$username     = get_username();
$searched     = in('searched', null, 'no filter'); // Don't filter the search setting.
$list_by_rank = ($searched && substr_compare($searched, '#', 0, 1) === 0); // Whether the search is by rank.



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




$page 		 = in('page', ceil(($rank_spot - $dead_count) / $record_limit));

if (!$searched && $hide_setting != $hide) { SESSION::set('hide_dead', $hide); } // Save the toggled state for later.






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
		$where_clause = "WHERE (rankings.uname ilike :param".count($queryParams).")";
		$queryParams[] = $searched.'%';
	} else if (!$list_by_rank) {
		$where_clause = "WHERE (rankings.uname ~* :param".count($queryParams).")";
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

// Determine the current page spot navigated to.
// If searching, use the page between 
// If no specific rank was requested, use the viewer's rank
// If a certain rank was requested, use that rank spot.
// Determine the page, if the dead count is more than the rank spot, default to 1, otherwise use the input page.
// Number of pages = ceil($totalrows / $record_limit);
// limit value = ($page * $record_limit) - $record_limit;

if ($searched && $list_by_rank) {
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






// Get the ninja information to create the lists.
$sel = "SELECT rank_id, rankings.uname, class.class_name as class, rankings.level, rankings.alive, rankings.days, clan_player._clan_id AS clan_id, clan.clan_name, players.player_id
	FROM rankings LEFT JOIN clan_player ON player_id = _player_id LEFT JOIN clan ON clan_id = _clan_id JOIN players on rankings.player_id = players.player_id JOIN class on class.class_id = players._class_id ".$where_clause." ORDER BY rank_id ASC, player_id ASC
	LIMIT $record_limit OFFSET $limitvalue";

$ninja_info = DatabaseConnection::$pdo->prepare($sel);

for ($i = 0;$i < count($queryParams); $i++) {	// *** Reformulate if queryParams gets to be more than 3 or for items
	$ninja_info->bindValue(':param'.$i, $queryParams[$i]);
}

$ninja_info->execute();




// Display the search form.
ob_start();
display_search_form($hide, $page, $searched, $dead_count);
$search_form = ob_get_contents();
ob_end_clean();
	
// Display the nav
ob_start();
player_list_nav($page, $hide, $searched, $record_limit, $totalrows, $numofpages);
$player_list_nav = ob_get_contents();
ob_end_clean();


// Display the recently-active-ninja section.

$active_ninja = '';
if (!$searched) { // Will not display active ninja on a search page.
	$active_ninja = render_active(5, $alive_only); // Display the currently active ninjas
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
		, 'clan_id'       => $a_player['clan_id']
		, 'clan_name'     => $a_player['clan_name']
		, 'alive'         => ($a_player['alive'] ? "&nbsp;" : "Dead"), // alive/dead display
	);
	return $row;
}

// Format each of the player rows, then just pass 'em to the template.

$ninja_count = 0;
$player_rows = '';
$ninja_rows = array();
while ($a_player = $ninja_info->fetch()) {
	$ninja_rows[] = format_ninja_row($a_player);
	$ninja_rows[$ninja_count]['odd_or_even'] = (($ninja_count+1) % 2 ? "odd" : "even");
	$ninja_count++;
}

$parts = get_certain_vars(get_defined_vars(), $whitelist=array('ninja_rows'));
echo render_template('player_list.tpl', $parts);

include SERVER_ROOT."interface/footer.php";

?>
