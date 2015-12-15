<?php
$private    = true;
$alive      = false;

if ($error = init($private, $alive)) {
	display_error($error);
} else {
	$char = new Player(self_char_id());
	$events = get_events($char->id(), 300);

	// Check for clan to use it in the nav tabs.
	$has_clan  = (bool)get_clan_by_player_id($char->id());

	read_events($char_id); // mark events as viewed.

	display_page(
		'events.tpl'
		, 'Events'
		, ['events'=>$events, 'has_clan'=>$has_clan, 'char'=>$char]
		, array(
			'quickstat' => true
		)
	);
}
