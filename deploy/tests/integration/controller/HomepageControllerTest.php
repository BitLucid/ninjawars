<?php
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use NinjaWars\core\environment\RequestWrapper;
use NinjaWars\core\extensions\SessionFactory;
use NinjaWars\core\extensions\StreamedViewResponse;
use NinjaWars\core\control\HomepageController;

class HomepageControllerTest extends PHPUnit_Framework_TestCase {
    private $controller;

	protected function setUp() {
		SessionFactory::init(new MockArraySessionStorage());
        $char_id = TestAccountCreateAndDestroy::create_testing_account();
		SessionFactory::getSession()->set('player_id', $char_id);
        $this->controller = new HomepageController();
    }

	protected function tearDown() {
        RequestWrapper::destroy();
        $session = SessionFactory::getSession();
        $session->invalidate();
    }

    public function testIndex() {
        $response = $this->controller->index();

        $this->assertInstanceOf(StreamedViewResponse::class, $response);
    }
}
