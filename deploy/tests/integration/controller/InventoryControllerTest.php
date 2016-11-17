<?php
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use NinjaWars\core\environment\RequestWrapper;
use NinjaWars\core\control\InventoryController;
use NinjaWars\core\extensions\SessionFactory;
use NinjaWars\core\data\Inventory;

class InventoryControllerTest extends NWTest {
    private $char;
    private $controller;
    private $inventory;
    const ITEM = 'caltrop';

	public function setUp() {
        parent::setUp();
        $this->controller = new InventoryController();
        $this->char = TestAccountCreateAndDestroy::char();
        $this->inventory = new Inventory($this->char);
        $request = new Request([], []);
        RequestWrapper::inject($request);
		SessionFactory::init(new MockArraySessionStorage());
        $sess = SessionFactory::getSession();
        $sess->set('player_id', $this->char->id());
        $this->inventory->add(self::ITEM);
	}

	public function tearDown() {
        $this->inventory->remove(self::ITEM);
        TestAccountCreateAndDestroy::destroy();
        RequestWrapper::inject(new Request([]));
        $session = SessionFactory::getSession();
        $session->invalidate();
        parent::tearDown();
    }

    public function testControllerCanBeInstantiatedWithoutError() {
        $this->assertInstanceOf('NinjaWars\core\control\InventoryController', $this->controller);
    }

    public function testIndexDoesNotErrorWithoutItem() {
        $this->inventory->remove(self::ITEM);
        $result = $this->controller->index($this->m_dependencies);
        $this->assertNotEmpty($result);
    }

    public function testIndexDoesNotErrorWithItem() {
        $result = $this->controller->index($this->m_dependencies);
        $this->assertNotEmpty($result);
    }
}
