<?php
require_once(LIB_ROOT."specific/lib_status.php"); // Status alterations.
init(); // Initialize the environment.

// TODO: Protect this file from unlogged-in displaying.

// *** Turning the header variables into variables for this page.
$section_only = in('section_only'); // Check whether it's an ajax section.
$command      = in('command');
$user_id = get_user_id();
$info = get_player_info();
$health       = $info['health'];
$strength     = $info['strength'];
$gold         = $info['gold'];
$kills        = $info['kills'];
$turns        = $info['turns'];
$level        = $info['level'];
$class        = $info['class'];
$bounty       = $info['bounty'];
$status       = get_status_array();  //The status variable is an array, of course.
$username     = get_username();
$next_level   = (getLevel($username) * 5); // This needs to have a more centralized formula source.
$max_health   = max_health_by_level($level);
$progress     = min(100, round(($kills/$next_level)*100));
$health_pct   = min(100, round(($health/$max_health)*100));

$status_output_list = render_status_list();

$viewinv = ($command == 'viewinv');

$items = query("SELECT item, amount FROM inventory WHERE owner = :user ORDER BY item", array(':user'=>$user_id));

$parts = get_certain_vars(get_defined_vars(), array('items')); // Pull current flat vars + the resultset into the template.

display_page('quickstats.tpl', "Quickstats", $parts, $options=array('alive'=>false, 'private'=>true, 'quickstat'=>false));

?>
