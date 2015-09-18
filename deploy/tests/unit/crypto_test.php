<?php
// Note that the file has to have a file ending of ...test.php to be run by phpunit


class TestCrypto extends PHPUnit_Framework_TestCase {

	function setUp(){
	}
	
	function tearDown(){
    }

    public function testRandomStringDoesntEverMatch(){
        $this->assertNotEmpty(make_random_string());
        $i = 30;
        while($i--){
            $this->assertNotEquals(make_random_string(), make_random_string());
        }
    }

    public function testNonceDoesntEverMatch(){
        $nonce = nonce();
        $this->assertNotEmpty(nonce());
        $i = 30;
        while($i--){
            $this->assertNotEquals(nonce(), nonce());
        }
    }


}

