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
    const ITEM = 'caltrops';

	public function setUp():void{
        parent::setUp();
        $this->controller = new InventoryController();
        $this->char = TestAccountCreateAndDestroy::char();
        $this->inventory = new Inventory($this->char);
        $request = new Request([], []);
        RequestWrapper::inject($request);
		SessionFactory::init(new MockArraySessionStorage());
        $sess = SessionFactory::getSession();
        $sess->set('player_id', $this->char->id());
        $this->inventory->add(self::ITEM, 1);
	}

	public function tearDown():void {
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

    /**
     * @group Inventory
     */
    public function testIndexOfInventoryDoesNotErrorWithoutItem() {
        $this->inventory->remove(self::ITEM, 9999); // It'll minimize to 0
        $result = $this->controller->index($this->m_dependencies);
        $this->assertNotEmpty($result);
    }

    /**
     * @group Inventory
     */
    public function testIndexOfInventoryDoesNotErrorWithItem() {
        $result = $this->controller->index($this->m_dependencies);
        $this->assertNotEmpty($result);
    }
}
