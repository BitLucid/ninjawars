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
/**
 * Initial inspiration from here: http://stackoverflow.com/questions/4145531/how-to-create-and-use-nonces
 */

/**
 * Create a hash from a random string.
 *
 * @return String
 */
function nonce() {
    // Fast hashing the random string only to make it a usable/passable nonce
    return hash('sha512', make_random_string());
}

/**
 * Seed random string for nonce
 *
 * @param int $bits Desired size of output
 * @return String
 */
function make_random_string($bits = 256) {
    $bytes = ceil($bits / 8);

    $return = '';
    for ($i = 0; $i < $bytes; $i++) {
        $return .= chr(mt_rand(0, 255));

    }
    return $return;
}
