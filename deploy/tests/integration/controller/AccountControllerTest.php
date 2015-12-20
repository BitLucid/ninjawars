<?php
require_once(CORE.'control/AccountController.php');

use Symfony\Component\HttpFoundation\Request;
use app\environment\RequestWrapper;
use app\Controller\AccountController as AccountController;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

class AccountControllerTest extends PHPUnit_Framework_TestCase {
	public function setUp() {
		nw\SessionFactory::init(new MockArraySessionStorage());
        $char_id = TestAccountCreateAndDestroy::create_testing_account();
		nw\SessionFactory::getSession()->set('player_id', $char_id);
	}

	public function tearDown() {
    }

    public function testAccountControllerCanInstantiateWithoutError() {
        $controller = new AccountController();
        $this->assertInstanceOf('app\Controller\AccountController', $controller);
    }

    public function testAccountControllerIndexRuns() {
        $controller = new AccountController();
        $response = $controller->index();
        $this->assertNotEmpty($response);
    }
}
