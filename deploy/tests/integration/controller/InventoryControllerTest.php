<?php
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use NinjaWars\core\environment\RequestWrapper;
use NinjaWars\core\control\InventoryController;
use NinjaWars\core\extensions\SessionFactory;

class InventoryControllerTest extends PHPUnit_Framework_TestCase {
    private $char;
    private $controller;
    const ITEM = 'caltrop';

	function setUp() {
        $this->controller = new InventoryController();
        $this->char = TestAccountCreateAndDestroy::char();
        $request = new Request([], []);
        RequestWrapper::inject($request);
		SessionFactory::init(new MockArraySessionStorage());
        $sess = SessionFactory::getSession();
        $sess->set('player_id', $this->char->id());
        add_item($this->char->id(), self::ITEM);
	}

	function tearDown() {
        removeItem($this->char->id(), self::ITEM);
        TestAccountCreateAndDestroy::destroy();
        RequestWrapper::inject(new Request([]));
        $session = SessionFactory::getSession();
        $session->invalidate();
    }

    public function testControllerCanBeInstantiatedWithoutError() {
        $this->assertInstanceOf('NinjaWars\core\control\InventoryController', $this->controller);
    }

    public function testIndexDoesNotErrorWithoutItem() {
        removeItem($this->char->id(), self::ITEM);
        $result = $this->controller->index();
        $this->assertNotEmpty($result);
    }

    public function testIndexDoesNotErrorWithItem() {
        $result = $this->controller->index();
        $this->assertNotEmpty($result);
    }
}
