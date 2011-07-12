<?php
$private    = true;
$alive      = false;

if ($error = init($private, $alive)) {
	display_error($error);
} else {
$user_id = get_user_id();
$events = get_events($user_id, 300);

$events = $events->fetchAll();

$has_clan  = !!get_clan_by_player_id($user_id);

read_events($user_id); // mark events as viewed.

display_page(
	'events.tpl'
	, 'Events'
	, get_certain_vars(get_defined_vars(), array('events'))
	, array(
		'quickstat' => false
	)
);
}
?>
