<?php
require_once(LIB_ROOT."control/lib_status.php"); // Status alterations.
init(true, false); // Initialize the environment.

// TODO: Protect this file from unlogged-in displaying.

// *** Turning the header variables into variables for this page.
$section_only = (in('section_only') === '1'); // Check whether it's an ajax section.
$command      = in('command');
$user_id      = get_user_id();
$info         = self_info();
$health       = ($user_id ? $info['health'] : 0);
$strength     = ($user_id ? $info['strength'] : 0);
$gold         = ($user_id ? $info['gold'] : 0);
$kills        = ($user_id ? $info['kills'] : 0);
$turns        = ($user_id ? $info['turns'] : 0);
$level        = ($user_id ? $info['level'] : 0);
$class        = ($user_id ? $info['class'] : 0);
$bounty       = ($user_id ? $info['bounty'] : 0);
$player       = new Player($user_id);
$username     = $player->vo->uname;
$next_level   = ($player->vo->level * 5); // This needs to have a more centralized formula source.
$max_health   = max_health_by_level($level);
$progress     = ($user_id ? min(100, round(($kills/$next_level)*100)) : 0);
$health_pct   = ($user_id ? min(100, round(($health/$max_health)*100)) : 0);

$status_list = get_status_list();

$viewinv = ($command == 'viewinv');

// *** TODO: switch this to query() when we switch to SMARTY. Templatelite can't handle iterating over the resultset ***
$items = query_array("SELECT item.item_display_name as item, amount FROM inventory join item on item_type = item.item_id WHERE owner = :user ORDER BY item.item_display_name", array(':user'=>$user_id));

$parts = get_certain_vars(get_defined_vars(), array('items', 'status_list')); // Pull current flat vars + the resultset into the template.

display_page(
	'quickstats.tpl'
	, 'Quickstats'
	, $parts
	, array(
		'quickstat'=>false
	)
);
