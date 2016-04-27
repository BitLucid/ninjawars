<?php

namespace NinjaWars\core\data;

/**
 * Cryptographic functionality
 * Initial inspiration from here: 
 * http://stackoverflow.com/questions/4145531/how-to-create-and-use-nonces
 */
class Crypto{
    /**
     * Create a hash from a random string.
     *
     * @return String
     */
    public static function nonce() {
        // Fast hashing the random str only to make it a usable/passable nonce
        return hash('sha512', static::make_random_string());
    }

    /**
     * Seed random string for nonce
     *
     * @param int $bits Desired size of output
     * @return String
     */
    public static function make_random_string($bits = 256) {
        $bytes = ceil($bits / 8);

        $return = '';
        for ($i = 0; $i < $bytes; $i++) {
            $return .= chr(mt_rand(0, 255));

        }
        return $return;
    }
}



