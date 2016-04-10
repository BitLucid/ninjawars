<?php
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use NinjaWars\core\environment\RequestWrapper;
use NinjaWars\core\extensions\SessionFactory;
use NinjaWars\core\control\AttackController;
use NinjaWars\core\data\Clan;
use NinjaWars\core\data\Player;

class AttackControllerTest extends PHPUnit_Framework_TestCase {
    private $controller;

    public function __construct() {
        $this->controller = new AttackController();
    }

	protected function setUp() {
		SessionFactory::init(new MockArraySessionStorage());
        $char_id = TestAccountCreateAndDestroy::create_testing_account();
		SessionFactory::getSession()->set('player_id', $char_id);
    }

	protected function tearDown() {
        RequestWrapper::destroy();
        TestAccountCreateAndDestroy::purge_test_accounts();
        $session = SessionFactory::getSession();
        $session->invalidate();
    }

    public function testAttackWithoutArgs() {
        $this->setExpectedException(\InvalidArgumentException::class);

        $response = $this->controller->index();
    }

    public function testAttackWithTarget() {
        $char_id_2 = TestAccountCreateAndDestroy::char_id_2();

        $request = Request::create('/attack', 'GET', ['target'=>$char_id_2]);
        RequestWrapper::inject($request);
        $response = $this->controller->index();

        $this->assertArrayHasKey('template', $response);
    }
    public function testDuelWithTarget() {
        $char_id_2 = TestAccountCreateAndDestroy::char_id_2();

        $params = [
            'target' => $char_id_2,
            'duel'   => 1,
        ];

        $request = Request::create('/attack', 'GET', $params);
        RequestWrapper::inject($request);
        $response = $this->controller->index();

        $this->assertArrayHasKey('template', $response);
    }
}
