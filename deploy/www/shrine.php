<?php
init(); // Initialize global settings.

$freeResLevelLimit = 6;
$freeResKillLimit  = 25;
$lostTurns         = 10; // *** Default turns lost when the player has no kills.
$startingKills     = 0;
$userLevel         = 0;
$quickstat         = 'player';
$poisoned          = getStatus($username) && isset($status_array['Poisoned']) && $status_array['Poisoned'];

if (isset($username)) {
	$startingKills     = getKills($username);
	$userLevel         = getLevel($username);
    $at_max_health     = ($players_health >= (150 + (($players_level - 1) * 25)));

	// *** A True or False as to whether resurrection will be free.
	$freeResurrection = ($userLevel < $freeResLevelLimit && $startingKills < $freeResKillLimit);
}	// End of username check.

render_page('shrine.tpl', 
        'Healing Shrine', 
        get_certain_vars(get_defined_vars(), array()), 
        $options=array('quickstat'=>true, 'private'=>false, 'alive'=>false)); 
?>
