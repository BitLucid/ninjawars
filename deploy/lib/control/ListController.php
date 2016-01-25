<?php
namespace NinjaWars\core\control;

use NinjaWars\core\control\SessionFactory;
use \Player as Player;

require_once(LIB_ROOT."control/lib_player_list.php");
require_once(LIB_ROOT."control/lib_player.php");

/**
 * Display the ninja list as a whole
 */
class ListController { 
    const ALIVE          = false;
    const PRIV           = false;

    public function __construct(){
    }

    /**
     * Get the ninja list and display it
     */
    public function index(){
$session      = SessionFactory::getSession();
$char = new Player(self_char_id());
$username     = $char->name();
$char_id      = $char->id();
$searched     = in('searched', null, 'no filter'); // Don't filter the search setting.
$list_by_rank = ($searched && substr_compare($searched, '#', 0, 1) === 0); // Whether the search is by rank.
$hide_setting = (!$searched && $session->has('hide_dead') ? $session->get('hide_dead') : 'dead'); // Defaults to hiding dead via session.
$hide         = ($searched ? 'none' : in('hide', $hide_setting)); // search override > get setting > session setting
$alive_only   = ($hide == 'dead');
$page         = in('page', 1); // Page will get changed down below.
$view_type    = in('view_type');
$page        = in('page');
$rank         = get_rank($char->id());
$alive_count  = 0;
$record_limit = 20; // *** The number of players that gets shown per page.

$dead_count = query_item("SELECT count(player_id) FROM rankings WHERE alive = false");



if (!$searched && $hide_setting != $hide) { // Save the toggled state for later.
    $session->set('hide_dead', $hide); 
} 

// Display the clear search and create the where clause for searching.

// If a search was made, specify letter or word-based search.
// If unless showing dead, check that health is > 0, or alive = true from the ranking.
// Otherwise, no searching was done, so the score

$where_clauses = []; // Array to add where clauses to.
// Select some players from the ranking.
$params = [];

if ($searched) {
    $view_type = 'searched';

    if (strlen($searched) == 1 || !$list_by_rank) {
        $where_clauses[] = " (rankings.uname ilike :searched || '%') ";
        $params[':searched'] = $searched;
    }
    if ($hide == 'dead') {
        $where_clause[] = " alive = true";
    }
}
else if ($hide == 'dead') {
    $where_clauses[] = " alive";
}

$query_count     = "SELECT count(player_id) FROM rankings "
    .(count($where_clauses)? "WHERE ".implode($where_clauses, ' AND ') : "");

$totalrows = query_item($query_count, $params);


$where_clauses[] = " active = 1"; 
// The rankings view automatically filters out inactives, but we have to do it manually when dealing directly with players table.

// ************************ Pagination **************************
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
$limitvalue = (int) max(0, ($page * $record_limit) - $record_limit);
$last_page = (($totalrows - ($record_limit * $page)) > 0);

// Get the ninja information to create the lists.
$sel = "SELECT rank_id, rankings.uname, class.class_name as class, class.identity as class_identity, class.theme as class_theme, rankings.level, rankings.alive, rankings.days, clan_player._clan_id AS clan_id, clan.clan_name, players.player_id
    FROM rankings LEFT JOIN clan_player ON player_id = _player_id LEFT JOIN clan ON clan_id = _clan_id JOIN players on rankings.player_id = players.player_id JOIN class on class.class_id = players._class_id ".(count($where_clauses)? " WHERE active = 1 AND ".implode($where_clauses, ' AND ') : "")." ORDER BY rank_id ASC, player_id ASC
    LIMIT :limit OFFSET :offset";

$params[':limit'] = $record_limit;
$params[':offset'] = $limitvalue;

$ninja_infos = query($sel, $params);



// Format each of the player rows, then just pass 'em to the template.

$ninja_count = 0;
$ninja_rows = [];


foreach($ninja_infos as $a_player){
    $ninja_rows[] = format_ninja_row($a_player);
    $ninja_rows[$ninja_count]['odd_or_even'] = (($ninja_count+1) % 2 ? "odd" : "even");
    $ninja_count++;
}

$active_ninjas = null;
if (!$searched) { // Will not display active ninja on a search page.
    $active_ninjas = get_active_players(5, $alive_only); // get  the currently active ninjas
}

    $parts = [
        'searched'=>$searched,
        'ninja_count'=>$ninja_count,
        'dead_count'=>$dead_count,
        'active_ninjas'=>$active_ninjas,
        'hide'=>$hide,
        'page'=>$page,
        'numofpages'=>$numofpages,
        'last_page'=>$last_page,
        'ninja_rows'=>$ninja_rows,
        ];

    $options = ['quickstat'=>true];
    $title = 'Ninja List';
    $template = 'list.tpl';

    return [
        'title'=>$title,
        'template'=>$template,
        'parts'=>$parts,
        'options'=>$options,
        ];
    }

}
