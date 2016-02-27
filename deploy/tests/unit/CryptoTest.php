<?php
class CryptoTest extends PHPUnit_Framework_TestCase {
    public function testRandomStringDoesntEverMatch() {
        $this->assertNotEmpty(make_random_string());
        $i = 30;

        while ($i--) {
            $this->assertNotEquals(make_random_string(), make_random_string());
        }
    }

    public function testNonceDoesntEverMatch() {
        nonce();
        $this->assertNotEmpty(nonce());
        $i = 30;

        while ($i--) {
            $this->assertNotEquals(nonce(), nonce());
        }
    }
}
