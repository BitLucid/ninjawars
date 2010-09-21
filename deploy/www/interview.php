<?php
$private    = false;
$alive      = false;

if ($error = init($private, $alive)) {
	display_error($error);
} else {
display_page(
	'interview.tpl'
	, 'Interview'
	, array()
	, false
);
}
?>
