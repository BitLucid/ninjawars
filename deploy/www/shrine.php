<?php
$private = true;
$alive   = false;

if ($error = init($private, $alive)) {
	display_error($error);
} else {

$player = new Player(self_char_id());
$freeResLevelLimit = 6;
$freeResKillLimit  = 25;
$lostTurns         = 10; // *** Default turns lost when the player has no kills.
$startingKills     = 0;
$userLevel         = 0;
$poisoned          = $player->hasStatus(POISON);

if (isset($username)) {
	$startingKills     = $player->vo->kills;
	$userLevel         = $player->level();
	$at_max_health     = ($player->vo->health >= $player->max_health());
	$player_health     = $player->health();

	// *** A True or False as to whether resurrection will be free.
	$freeResurrection = ($userLevel < $freeResLevelLimit && $startingKills < $freeResKillLimit);
}	// End of username check.

display_page(
	'shrine.tpl' // *** Main Template ***
	, 'Healing Shrine' // *** Page Title ***
	, get_certain_vars(get_defined_vars(), array()) // *** Page Variables ***
	, array( // *** Page Options ***
		'quickstat' => 'player'
	)
); 
}
