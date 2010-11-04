<?php
$private = false;
$alive   = false;

if ($error = init($private, $alive)) {
	display_error($error);
} else {

display_page(
	'staff.tpl' // *** Main Template ***
	, 'Ninjawars Staff' // *** Page Title ***
	, get_certain_vars(get_defined_vars(), array()) // *** Page Variables ***
	, array( // *** Options ***
		'quickstat'    => false
	)
);
}
?>
