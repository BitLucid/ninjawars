<?php
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use NinjaWars\core\environment\RequestWrapper;
use NinjaWars\core\extensions\SessionFactory;
use NinjaWars\core\extensions\StreamedViewResponse;
use NinjaWars\core\control\EventsController;

class EventsControllerTest extends NWTest {
    private $controller;

	public function setUp() {
        parent::setUp();
        $this->controller = new EventsController();
		SessionFactory::init(new MockArraySessionStorage());
        $char_id = TestAccountCreateAndDestroy::create_testing_account();
		SessionFactory::getSession()->set('player_id', $char_id);
    }

	public function tearDown() {
        parent::tearDown();
        RequestWrapper::destroy();
        $session = SessionFactory::getSession();
        $session->invalidate();
    }

    public function testIndex() {
        $response = $this->controller->index();

        $this->assertInstanceOf(StreamedViewResponse::class, $response);
    }
}
