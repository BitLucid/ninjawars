<?php
use NinjaWars\core\environment\RequestWrapper;
use Symfony\Component\HttpFoundation\Request;
use NinjaWars\core\Filter;

/**
 * Input function that by default LEAVES INPUT COMPLETELY UNFILTERED
 * To not filter some input, you have to explicitly pass in null for the third parameter,
 * e.g. in('some_url_parameter', null, null)
 */
function in($var_name, $default_val=null, $filter_callback=null) {
	$req = RequestWrapper::getPostOrGet($var_name);
	$result = (isset($req) ? $req : $default_val);

	// Check that the filter function sent in exists.
	if ($filter_callback) {
		$result = Filter::$filter_callback($result);
	}

    return $result;
}

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
