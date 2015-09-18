<?php
/**
 * Initial inspiration from here: http://stackoverflow.com/questions/4145531/how-to-create-and-use-nonces
 **/

/**
 * Create a hash from a random string.
 **/
function nonce(){
	// Fast hashing the random string only to make it a usable/passable nonce
	return hash('sha512', make_random_string());
}

/**
 * Seed random string for nonce
 **/
function make_random_string($bits = 256) {
    $bytes = ceil($bits / 8);
    $return = '';
    for ($i = 0; $i < $bytes; $i++) {
        $return .= chr(mt_rand(0, 255));
    }
    return $return;
}