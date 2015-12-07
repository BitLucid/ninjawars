<?php
require_once(CORE.'control/AccountController.php');
use Symfony\Component\HttpFoundation\Request;
use app\environment\RequestWrapper;
use app\Controller\AccountController as AccountController;


class AccountControllerTest extends PHPUnit_Framework_TestCase {


	public function setUp(){
	}
	
	public function tearDown(){
    }

    public function testAccountControllerCanInstantiateWithoutError(){
        $controller = new AccountController();
        $this->assertInstanceOf('app\Controller\AccountController', $controller);
    }
    
    public function testAccountControllerIndexRuns(){
        $controller = new AccountController();
        $this->markTestIncomplete('Need to be able to mock or inject session for AccountControllerTest.');
        $response = $controller->index();
        $this->assertNotEmpty($response);
    }
}

