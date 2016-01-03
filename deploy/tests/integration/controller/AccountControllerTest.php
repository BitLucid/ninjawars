<?php
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use NinjaWars\core\control\AccountController;
use NinjaWars\core\control\SessionFactory;
use app\environment\RequestWrapper;

class AccountControllerTest extends PHPUnit_Framework_TestCase {
	public function setUp() {
		SessionFactory::init(new MockArraySessionStorage());
        $char_id = TestAccountCreateAndDestroy::create_testing_account();
		SessionFactory::getSession()->set('player_id', $char_id);
	}

	public function tearDown() {
    }

    public function testAccountControllerCanInstantiateWithoutError() {
        $controller = new AccountController();
        $this->assertInstanceOf('NinjaWars\core\control\AccountController', $controller);
    }

    public function testAccountControllerIndexRuns() {
        $controller = new AccountController();
        $response = $controller->index();
        $this->assertNotEmpty($response);
    }
}
