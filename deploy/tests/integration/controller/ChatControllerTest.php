<?php
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use NinjaWars\core\environment\RequestWrapper;
use NinjaWars\core\extensions\SessionFactory;
use NinjaWars\core\extensions\StreamedViewResponse;
use NinjaWars\core\control\ChatController;

class ChatControllerTest extends NWTest {
    private $controller;

	public function setUp() {
        parent::setUp();
        $this->controller = new ChatController();
        $this->login();
    }

	public function tearDown() {
        RequestWrapper::destroy();
        $this->mockLogout();
    }

    public function testIndex() {
        RequestWrapper::inject(new Request());
        $response = $this->controller->index();

        $this->assertInstanceOf(StreamedViewResponse::class, $response);
    }

    public function testIndexRendersEvenLoggedOut() {
        $response = $this->controller->index($this->mockLogout());

        $this->assertInstanceOf(StreamedViewResponse::class, $response);
    }
}
