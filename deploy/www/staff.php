<?php
$private = false;
$alive   = false;

if ($error = init($private, $alive)) {
	display_error($error);
} else {

$mailto = 
	"<a href=".
	"\"mailto:".
	htmlentities(rawurlencode("'".SUPPORT_EMAIL_NAME."' <".SUPPORT_EMAIL.">")).
	'?subject='.rawurlencode('NinjaWars question: ')."\"".
	">".out(SUPPORT_EMAIL_NAME)." &lt;".SUPPORT_EMAIL."&gt;</a>";

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
