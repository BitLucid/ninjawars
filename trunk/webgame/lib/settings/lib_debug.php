<?php
/**
 * Functions to register and deregister a debug variable for live debugging.
 * Pretty much should be included explicitly on a page to allow live debugging on that page.
 * 
 * @category debug
 * @package lib
 * @subpackage settings
 * @link
 */
 
// *** This function call turns on or off debugging.
check_for_debug();
 
 
function check_for_debug(){
	if (isset($_GET['debug']) && $_GET['debug'] == 'on'){
		$_COOKIE['debug'] == true;
	} else if (isset($_GET['debug']) && $_GET['debug'] == 'off'){
		$_COOKIE['debug'] == false;
	}
}


function nw_debug(){
	$result = false;
	if (DEBUG){
		$result = true;
	}
	if ($_COOKIE['debug']){
		$result = true;
	}
	return $result;
}


?>
