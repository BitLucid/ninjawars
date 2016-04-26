<?php
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use NinjaWars\core\environment\RequestWrapper;
use NinjaWars\core\extensions\SessionFactory;
use NinjaWars\core\extensions\StreamedViewResponse;
use NinjaWars\core\control\MessagesController;

class MessagesControllerTest extends PHPUnit_Framework_TestCase {
    private $controller;

    public function __construct() {
        $this->controller = new MessagesController();
    }

	protected function setUp() {
		SessionFactory::init(new MockArraySessionStorage());
        $char_id = TestAccountCreateAndDestroy::create_testing_account();
		SessionFactory::getSession()->set('player_id', $char_id);
    }

	protected function tearDown() {
        RequestWrapper::destroy();
        $session = SessionFactory::getSession();
        $session->invalidate();
    }

    public function testViewPersonal() {
        RequestWrapper::inject(new Request());
        $response = $this->controller->viewPersonal();
        $this->assertInstanceOf(StreamedViewResponse::class, $response);
    }

    public function testViewClan() {
        RequestWrapper::inject(new Request());
        $response = $this->controller->viewClan();
        $this->assertInstanceOf(StreamedViewResponse::class, $response);
    }
}
