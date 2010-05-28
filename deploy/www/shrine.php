<?php
$private = false;
$alive   = false;

if ($error = init($private, $alive)) {
	display_error($error);
} else {

$freeResLevelLimit = 6;
$freeResKillLimit  = 25;
$lostTurns         = 10; // *** Default turns lost when the player has no kills.
$startingKills     = 0;
$userLevel         = 0;
$poisoned          = getStatus($username) && isset($status_array['Poisoned']) && $status_array['Poisoned'];

if (isset($username)) {
	$startingKills     = getKills($username);
	$userLevel         = getLevel($username);
	$at_max_health     = ($players_health >= (150 + (($players_level - 1) * 25)));

	// *** A True or False as to whether resurrection will be free.
	$freeResurrection = ($userLevel < $freeResLevelLimit && $startingKills < $freeResKillLimit);
}	// End of username check.

display_page(
	'shrine.tpl' // *** Main Template ***
	, 'Healing Shrine' // *** Page Title ***
	, get_certain_vars(get_defined_vars(), array()) // *** Page Variables ***
	, array( // *** Page Options ***
		'quickstat' => 'player'
		, 'private' => $private
		, 'alive'   => $alive
	)
); 
}
?>
