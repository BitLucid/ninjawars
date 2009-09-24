<?php
/**
 * Make all warnings and notices and stuff be made visible.
 * 
 * @package lib
 * @subpackage settings
**/

if (DEBUG && DEBUG_ALL_ERRORS) {
	error_reporting(E_ALL | E_STRICT);
	//error_reporting(E_ALL); // Most settings.
	// error_reporting(E_ALL | E_STRICT); // Completely everything.
}

/**
 * Sets a strict level of error reporting when local debug is on.
 * Probably not necessary with the setting run above.
**/
function start_nw_error_reporting(){
	$original_error_reporting = error_reporting();
	if (DEBUG) {
		error_reporting(E_ALL | E_STRICT); //error_reporting(E_ALL);
		// Turns on strict standards and notices.
	}
	return $original_error_reporting;
}


?>
