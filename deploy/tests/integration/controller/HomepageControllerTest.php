<?php
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use NinjaWars\core\environment\RequestWrapper;
use NinjaWars\core\extensions\SessionFactory;
use NinjaWars\core\extensions\StreamedViewResponse;
use NinjaWars\core\control\HomepageController;

class HomepageControllerTest extends NWTest {
    private $controller;

	public function setUp() {
        parent::setUp();
		SessionFactory::init(new MockArraySessionStorage());
        $char_id = TestAccountCreateAndDestroy::create_testing_account();
		SessionFactory::getSession()->set('player_id', $char_id);
        $this->controller = new HomepageController();
    }

	public function tearDown() {
        RequestWrapper::destroy();
        $session = SessionFactory::getSession();
        $session->invalidate();
        parent::tearDown();
    }

    public function testIndex() {
        $response = $this->controller->index($this->m_dependencies);

        $this->assertInstanceOf(StreamedViewResponse::class, $response);
    }
}
