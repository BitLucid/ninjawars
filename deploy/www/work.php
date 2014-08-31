<?php
require_once(LIB_ROOT.'control/lib_inventory.php');
$private   = false;
$alive     = true;

if ($error = init($private, $alive)) {
	display_error($error);
} else {

$work_multiplier        = 30;
$worked                 =
$new_gold               =
$not_enough_energy      =
$use_second_description = null;
$is_logged_in           = is_logged_in();

$worked = intval(in('worked'));

$recommended_to_work = 10;

// Store or retrieve the last value of turns worked.
if ($worked && is_numeric($worked)) {
	set_setting('turns_worked', $worked);
	$recommended_to_work = $worked;
} else {
	$last_worked = get_setting('turns_worked');
	$recommended_to_work = $last_worked? $last_worked : 10;
}

// Work only if the work was requested, not just if the setting was set.
if ($worked > 0) {
	$turns = get_turns($char_id);

	if ($worked > $turns) {
	    $not_enough_energy = true;
	} else {
		$new_gold  = $worked * $work_multiplier;   // *** calc amount worked ***

		add_gold($char_id, $new_gold);
		$turns = subtractTurns($char_id, $worked);

		$use_second_description = true;
	}
}

$gold  = get_gold($char_id); // Get the current/final gold.
$gold_display = number_format($gold);

display_page(
	'work.tpl' // *** Main Template ***
	, 'Working in the Village' // *** Page Title ***
	, get_certain_vars(get_defined_vars(), array()) // *** Page Variables ***
	, array( // *** Page Options ***
		'quickstat' => 'player'
	)
);
}
?>
