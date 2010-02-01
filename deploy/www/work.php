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

$worked = intval(in('worked'));

if ($worked > 0){
	$turns = getTurns($username);
	$gold  = getGold($username);

	if ($worked > $turns){
	    $not_enough_energy = true;
	} else {
		$new_gold  = $worked * $work_multiplier;   // *** calc amount worked ***

		$gold  = addGold($username, $new_gold);
		$turns = subtractTurns($username, $worked);
		
		$use_second_description = true;
	}
}

echo render_page('work.tpl', 
        'Working in the Village', 
        get_certain_vars(get_defined_vars(), array()), 
        $options=array('quickstat'=>'player', 'private'=>false, 'alive'=>true)); 
        
?>
