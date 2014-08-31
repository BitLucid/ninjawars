<?php
$private    = true;
$alive      = false;

if ($error = init($private, $alive)) {
	display_error($error);
} else {
$char_id = self_char_id();
$events = get_events($char_id, 300);

$events = $events->fetchAll();

$player = new Player($char_id);


$has_clan  = (bool)get_clan_by_player_id($char_id) || $player->isAdmin();

read_events($char_id); // mark events as viewed.

display_page(
	'events.tpl'
	, 'Events'
	, get_certain_vars(get_defined_vars(), array('events'))
	, array(
		'quickstat' => false
	)
);
}
