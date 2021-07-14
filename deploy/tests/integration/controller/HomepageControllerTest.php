<?php
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use NinjaWars\core\environment\RequestWrapper;
use NinjaWars\core\extensions\SessionFactory;
use NinjaWars\core\extensions\StreamedViewResponse;
use NinjaWars\core\control\HomepageController;

class HomepageControllerTest extends NWTest {
    private $controller;

	public function setUp():void {
        parent::setUp();
        $this->login();
        $this->controller = new HomepageController();
    }

	public function tearDown():void {
        $this->mockLogout();
        parent::tearDown();
    }

    public function testIndex() {
        $response = $this->controller->index($this->m_dependencies);

        $this->assertInstanceOf(StreamedViewResponse::class, $response);
    }


    public function testIndexWorksEvenLoggedOut() {
        $response = $this->controller->index($this->mockLogout());

        $this->assertInstanceOf(StreamedViewResponse::class, $response);
    }
}
