<?php
$alive      = false;
$private    = true;
$quickstat  = false;
$page_title = "Quickstats";

include_once(SERVER_ROOT."interface/header.php");
require_once(LIB_ROOT."specific/lib_status.php"); // Status alterations.

// *** Turning the header variables into variables for this page.
$section_only = in('section_only'); // Check whether it's an ajax section.
$command  = in('command');
$health   = $players_health;
$strength = $players_strength;
$gold     = $players_gold;
$kills    = $players_kills;
$turns    = $players_turns;
$level    = $players_level;
$class    = $players_class;
$bounty   = $players_bounty;
$status   = $players_status;  //The status variable is an array, of course.
$username = get_username();

$status_output_list = render_status_list();

$health_section = render_health_section($health);

$viewinv = $command == 'viewinv'? true : false;

$sql->Query("SELECT item, amount FROM inventory WHERE owner = '".sql($username)."' ORDER BY item");

$items_section = '';

foreach($sql->FetchAll() AS $loopItem){
    if($loopItem['amount']){ // Skip zero counts.
    $items_section .= "
	          <tr><td> {$loopItem['item']}: </td>
	          <td> {$loopItem['amount']}</td></tr>\n";
	}
}

$parts = get_certain_vars(get_defined_vars()); // Pull current flat vars into the template.

echo render_template('quickstats.tpl', $parts);

if(!$section_only){
    ?>
    </body>
    </html>
    <?php
}
?>
