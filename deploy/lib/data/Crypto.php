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
        return bin2hex(random_bytes(32));
    }
}



