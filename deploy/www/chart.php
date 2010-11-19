<?php
$alive      = false;
$private    = true;

if ($error = init($private, $alive)) {
	display_error($error);
} else {

require_once(LIB_ROOT."control/lib_player.php"); // To pull the maximum_level();

$level_chart = 1;
$kills_chart = 0;
$str_chart   = 5;
$hp_chart    = 150;
$max_level   = maximum_level()+1;
$max_hp      = 150 + (($max_level - 1) * 25);

display_page(
	'chart.tpl'
	, 'Advancement Chart'
	, get_defined_vars()
	, null
);
}
?>
