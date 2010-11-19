<?php
/**
 * Return first non-null argument.
**/
function first_value() {
	$arg_list = func_get_args();
	foreach ($arg_list as $l_arg) {
		if (!is_null($l_arg)) {
			return $l_arg;
		}
	}

	return null;
}

/**
 * Much more easy-going, just:
 * Return first true-like argument.
**/
function whichever() {
	$arg_list = func_get_args();
	foreach ($arg_list as $l_arg) {
		if ($l_arg != false) {
			return $l_arg;
		}
	}

	return null;
}
?>
