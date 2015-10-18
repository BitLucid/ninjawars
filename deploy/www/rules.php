<?php
$alive      = false;
$private    = false;

if ($error = init($private, $alive)) {
	display_error($error);
} else {
display_page(
	'rules.tpl'
	, 'Rules'
	, array()
	, false
);
}
