<?php
use NinjaWars\core\control\LoginController;

class LoginControllerTest extends PHPUnit_Framework_TestCase {
	public function setUp() {
	}

	public function tearDown() {
    }

    public function testLoginControllerCanBeInstantiatedWithoutError() {
        $controller = new LoginController();
        $this->assertInstanceOf('NinjaWars\core\control\LoginController', $controller);
    }

    public function testLoginWithGibberishFails() {
        $controller = new LoginController();
        $error_message = $controller->performLogin('gibber', 'ish');
        $this->assertNotEmpty($error_message);
    }
}
