<?php
$alive      = false;
$private    = true;
$quickstat  = false;
$page_title = "Quickstats";

include_once(SERVER_ROOT."interface/header.php");
require_once(LIB_ROOT."specific/lib_status.php"); // Status alterations.

// *** Turning the header variables into variables for this page.
$section_only = (in('section_only') === '1'); // Check whether it's an ajax section.
$command      = in('command');
$health       = $players_health;
$strength     = $players_strength;
$gold         = $players_gold;
$kills        = $players_kills;
$turns        = $players_turns;
$level        = $players_level;
$class        = $players_class;
$bounty       = $players_bounty;
$status       = $players_status;  //The status variable is an array, of course.
$username     = get_username();
$next_level   = (getLevel($username) * 5);
$max_health   = max_health_by_level($level);
$progress     = min(100, round(($kills/$next_level)*100));
$health_pct   = min(100, round(($health/$max_health)*100));

$status_output_list = render_status_list();

$viewinv = ($command == 'viewinv');

$inv_resultset = query_resultset("SELECT item, amount FROM inventory WHERE owner = :user ORDER BY item", array(':user'=>get_user_id($username)));

$items_section = '';

// TODO: Change this and the template to be using dl/dd/dt instead of a table.

foreach($inv_resultset as $loopItem) {
	if ($loopItem['amount']) { // Skip zero counts.
		$items_section .= "
	          <tr><td> {$loopItem['item']}: </td>
	          <td> {$loopItem['amount']}</td></tr>\n";
	}
}

$parts = get_certain_vars(get_defined_vars()); // Pull current flat vars into the template.

echo render_template('quickstats.tpl', $parts);
?>
