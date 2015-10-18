<?php
$private = false;
$alive   = false;

if ($error = init($private, $alive)) {
	display_error($error);
} else {
	display_page(
		'about.tpl'	// *** Main Template ***
		, 'About NinjaWars' // *** Page Title ***
		, get_certain_vars(get_defined_vars(), array()) // *** Page Variables ***
		, array( // *** Page Options ***
			'quickstat' => false
		)
	);
}
