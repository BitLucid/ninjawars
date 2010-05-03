<?php
$private    = false;
$alive      = true;
$quickstat  = "player";
$page_title = "Working in the Village";

init();

$work_multiplier = 30;
$worked = null;
$new_gold = null;
$not_enough_energy = null;
$use_second_description = null;
$is_logged_in = is_logged_in();

$worked = in('worked');
$worked = intval($worked);

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
	$turns = getTurns($username);
	$gold  = getGold($username);

	if ($worked > $turns) {
	    $not_enough_energy = true;
	} else {
		$new_gold  = $worked * $work_multiplier;   // *** calc amount worked ***

		$gold  = addGold($username, $new_gold);
		$turns = subtractTurns($username, $worked);

		$use_second_description = true;
	}
}

render_page('work.tpl', 
        'Working in the Village', 
        get_certain_vars(get_defined_vars(), array()), 
        $options=array('quickstat'=>'player', 'private'=>false, 'alive'=>true)); 
?>
