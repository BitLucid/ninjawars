<?php

use NinjaWars\core\data\Crypto;

class CryptoTest extends PHPUnit_Framework_TestCase {

    public function testNonceDoesntEverMatch() {
        $this->assertNotEmpty(Crypto::nonce());
        $i = 30;

        while ($i--) {
            $this->assertNotEquals(Crypto::nonce(), Crypto::nonce());
        }
    }
}
