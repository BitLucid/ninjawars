<?php

use NinjaWars\core\data\Crypto;

class CryptoTest extends NWTest {

    public function testNonceDoesntEverMatch() {
        $this->assertNotEmpty(Crypto::nonce());
        $i = 30;

        while ($i--) {
            $this->assertNotEquals(Crypto::nonce(), Crypto::nonce());
        }
    }
}
