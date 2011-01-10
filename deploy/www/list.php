<?php
$private = false;
$alive   = false;

if ($error = init($private, $alive)) {
	display_error($error);
} else {

require_once(LIB_ROOT."control/lib_player_list.php");
require_once(LIB_ROOT."control/lib_player.php");

DatabaseConnection::getInstance();

$username     = get_username();
$searched     = in('searched', null, 'no filter'); // Don't filter the search setting.
$list_by_rank = ($searched && substr_compare($searched, '#', 0, 1) === 0); // Whether the search is by rank.

$hide_setting = (!$searched && SESSION::is_set('hide_dead') ? SESSION::get('hide_dead') : 'dead'); // Defaults to hiding dead via session.
$hide         = ($searched ? 'none' : in('hide', $hide_setting)); // search override > get setting > session setting

$alive_only   = ($hide == 'dead');
$page         = in('page', 1); // Page will get changed down below.
$alive_count  = 0;
$record_limit = 20; // *** The number of players that gets shown per page.
$view_type    = in('view_type');
$rank         = get_rank($username);

$dead_count = query_item("SELECT count(player_id) FROM rankings WHERE alive = false");

$page 		 = in('page');

if (!$searched && $hide_setting != $hide) { SESSION::set('hide_dead', $hide); } // Save the toggled state for later.

// Display the clear search and create the where clause for searching.

// If a search was made, specify letter or word-based search.
// If unless showing dead, check that health is > 0, or alive = true from the ranking.
// Otherwise, no searching was done, so the score

$where_clauses = array(); // Array to add where clauses to.


// Select some players from the ranking.
$queryParams = array();

if ($searched) {
	$view_type = 'searched';

	if (strlen($searched) == 1) {
		$where_clauses[] = " (rankings.uname ilike :param".count($queryParams).") ";
		$queryParams[] = $searched.'%';
	} else if (!$list_by_rank) {
		$where_clauses[] = " (rankings.uname ~* :param".count($queryParams).") ";
		$queryParams[] = $searched;
	}

	if ($hide == 'dead') {
		$where_clause[] = " alive = true";
	}
}
else if ($hide == 'dead') {
	$where_clauses[] = " alive";
}

$query_count     = "SELECT count(player_id) FROM rankings ".(count($where_clauses)? "WHERE ".implode($where_clauses, ' AND ') : "");
$count_statement = DatabaseConnection::$pdo->prepare($query_count);

$where_clauses[] = " active = 1"; // Filter out inactives when not dealing with rankings, which is filtered separately.

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
	$page = ceil(substr($searched, 1) / $record_limit);
} else if ($page == "searched") {
	$page = in('page', 1);
} else {
	$page = ($page < 1 ? 1 : $page); // Prevent the page number from going negative.
}

$numofpages = ceil($totalrows / $record_limit);
$limitvalue = max(0, ($page * $record_limit) - $record_limit);

// Get the ninja information to create the lists.
$sel = "SELECT rank_id, rankings.uname, class.class_name as class, class.identity as class_identity, class.theme as class_theme, rankings.level, rankings.alive, rankings.days, clan_player._clan_id AS clan_id, clan.clan_name, players.player_id
	FROM rankings LEFT JOIN clan_player ON player_id = _player_id LEFT JOIN clan ON clan_id = _clan_id JOIN players on rankings.player_id = players.player_id JOIN class on class.class_id = players._class_id ".(count($where_clauses)? " WHERE active = 1 AND ".implode($where_clauses, ' AND ') : "")." ORDER BY rank_id ASC, player_id ASC
	LIMIT :limit OFFSET :offset";

$ninja_info = DatabaseConnection::$pdo->prepare($sel);

for ($i = 0;$i < count($queryParams); $i++) {	// *** Reformulate if queryParams gets to be more than 3 or for items
	$ninja_info->bindValue(':param'.$i, $queryParams[$i]);
}

$ninja_info->bindValue(':limit', $record_limit);
$ninja_info->bindValue(':offset', $limitvalue);
$ninja_info->execute();

$last_page = (($totalrows - ($record_limit * $page)) > 0);

if (!$searched) { // Will not display active ninja on a search page.
	$active_ninjas = get_active_players(5, $alive_only); // get  the currently active ninjas
} else {
	$active_ninjas = null;
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

$parts = get_certain_vars(get_defined_vars(), $whitelist=array('ninja_rows', 'active_ninjas'));

display_page(
	'list.tpl'	// *** Main Template ***
	, 'Ninja List'		// *** Page Title ***
	, $parts			// *** Page Variables ***
	, array(			// *** Page Options ***
		'quickstat' => false
	)
);
}
?>
