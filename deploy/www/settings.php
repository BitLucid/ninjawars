<?php
$alive      = true;
$private    = true;

if ($error = init($private, $alive)) {
	display_error($error);
} else {

$settings = get_settings();
//var_dump($settings);
// TODO: Add a "don't use javascript" setting, mainly for the chat iframe.

display_page(
	'settings.tpl'
	, 'Settings'
	, get_certain_vars(get_defined_vars(), array())
	, array(
		'quickstat' => 'player'
	)
);

}
?>
