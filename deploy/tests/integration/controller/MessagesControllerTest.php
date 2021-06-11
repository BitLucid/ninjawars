<?php
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use NinjaWars\core\environment\RequestWrapper;
use NinjaWars\core\extensions\SessionFactory;
use NinjaWars\core\extensions\StreamedViewResponse;
use NinjaWars\core\control\MessagesController;

class MessagesControllerTest extends NWTest {
    private $controller;

	public function setUp():void {
        parent::setUp();
        $this->controller = new MessagesController();
		SessionFactory::init(new MockArraySessionStorage());
        $char_id = TestAccountCreateAndDestroy::create_testing_account();
		SessionFactory::getSession()->set('player_id', $char_id);
    }

	public function tearDown():void {
        RequestWrapper::destroy();
        $session = SessionFactory::getSession();
        $session->invalidate();
        parent::tearDown();
    }

    public function testViewPersonal() {
        RequestWrapper::inject(new Request());
        $response = $this->controller->viewPersonal($this->m_dependencies);
        $this->assertInstanceOf(StreamedViewResponse::class, $response);
    }

    public function testViewClan() {
        RequestWrapper::inject(new Request());
        $response = $this->controller->viewClan($this->m_dependencies);
        $this->assertInstanceOf(StreamedViewResponse::class, $response);
    }
}
