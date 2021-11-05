<?php
// Debug a value for html, if debugging is turned on.
function debug($val) {
    if (DEBUG) {
    	$vals = func_get_args();
		foreach ($vals as $fval) {
		    echo "<pre class='debug' style='font-size:12pt;background-color:white;color:black;position:relative;z-index:10'>";
			var_dump($fval);
		    echo "</pre>";
        }
    }
}

// Allow debugging by a setting in the cookie.
function nw_debug() {
	$result = false;

	if (DEBUG) {
		$result = true;
	}

	if (isset($_COOKIE['debug']) && $_COOKIE['debug']) {
		$result = true;
	}

	return $result;
}



