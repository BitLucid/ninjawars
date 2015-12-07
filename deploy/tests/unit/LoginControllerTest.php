<?php
require_once(CORE.'control/LoginController.php');

use app\Controller\LoginController;

class LoginControllerTest extends PHPUnit_Framework_TestCase {


	public function setUp(){
	}
	
	public function tearDown(){
    }

    public function testLoginControllerCanBeInstantiatedWithoutError(){
        $controller = new LoginController();
        $this->assertInstanceOf('app\Controller\LoginController', $controller);
    }

    public function testLoginWithGibberishFails(){
        $controller = new LoginController();
        $error_message = $controller->perform_login_if_requested($username_requested='gibber', $pass='ish');
        $this->assertNotEmpty($error_message);
    }

}

