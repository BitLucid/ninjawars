<?php
$alive   = false;
$private = false;

if ($error = init($private, $alive)) {
	display_error($error);
} else {
require_once(LIB_ROOT."specific/lib_player_list.php");

display_page(
	'main.tpl' // *** Main Template ***
	, 'Welcome to Ninjawars' // *** Page Title ***
	, get_certain_vars(get_defined_vars(), array()) // *** Page Variables ***
	, array( // *** Page Options ***
		'quickstat' => false
	)
);
}
?>
