<?php
$alive      = false;
$private    = false;

if ($error = init($private, $alive)) {
	display_error($error);
} else {
logout_user(); // From lib_auth (for authenticate)

display_template(
	'logout.tpl'
	, 'Log Out'
	, array()
	, false
);
}
?>
