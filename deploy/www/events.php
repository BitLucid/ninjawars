<?php
$private    = true;
$alive      = false;

if ($error = init($private, $alive)) {
	display_error($error);
} else {
$user_id = get_user_id();
$events = get_events($user_id, 300);

read_events($user_id); // mark events as viewed.

$event_list = '';
foreach($events as $loop_event) {
	$loop_event['message'] = out($loop_event['message']);
	$event_list .= render_template('single_event.tpl', array('event' => $loop_event));
}

if (!$event_list) {
	$event_list = 'You have not been attacked recently.';
}

display_page(
	'events.tpl'
	, 'Events'
	, get_certain_vars(get_defined_vars())
	, array(
		'quickstat' => false
	)
);

}
?>
