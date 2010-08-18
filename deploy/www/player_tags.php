<?php
$private    = false;
$alive      = false;

if ($error = init($private, $alive)) {
	display_error($error);
} else {

require_once(LIB_ROOT."specific/lib_player_list.php");
require_once(LIB_ROOT."specific/lib_player.php");

$player_size = player_size();

display_page(
	'player-tags.tpl'	// *** Main Template ***
	, 'Ninja List'	// *** Page Title ***
	, array('player_size' => $player_size)	// *** Page Variables ***
	, array(	// *** Page Options ***
		'quickstat' => false
	)
);
}
?>
