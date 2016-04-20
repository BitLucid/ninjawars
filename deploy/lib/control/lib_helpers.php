<?php
function debug($val) {
    if (DEBUG) {
    	$vals = func_get_args();
    	foreach($vals as $val){
		    echo "<pre class='debug' style='font-size:12pt;background-color:white;color:black;position:relative;z-index:10'>";
		    var_dump($val);
		    echo "</pre>";
        }
    }
}

function nw_debug() {
	$result = false;

	if (DEBUG) {
		$result = true;
	}

	if ($_COOKIE['debug']) {
		$result = true;
	}

	return $result;
}
