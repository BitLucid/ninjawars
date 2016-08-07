<?php
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use NinjaWars\core\environment\RequestWrapper;
use NinjaWars\core\extensions\SessionFactory;
use NinjaWars\core\extensions\StreamedViewResponse;
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
        $request = Request::create('/attack', 'GET', []);
        RequestWrapper::inject($request);

        $response = $this->controller->index();
        $this->assertInstanceOf(StreamedViewResponse::class, $response);
    }

    public function testAttackWithTarget() {
        $char_id_2 = TestAccountCreateAndDestroy::char_id_2();

        $request = Request::create('/attack', 'GET', ['target'=>$char_id_2]);
        RequestWrapper::inject($request);
        $response = $this->controller->index();

        $this->assertInstanceOf(StreamedViewResponse::class, $response);
    }

    public function testAttackDoesNotUseExcessiveTurns() {
        $char_id_2 = TestAccountCreateAndDestroy::char_id_2();
        $char_2 = Player::find($char_id_2);
        $initial_turns = $char_2->turns;

        $request = Request::create('/attack', 'GET', ['target'=>$char_id_2]);
        RequestWrapper::inject($request);
        $response = $this->controller->index();
        $char_2 = Player::find($char_id_2); // Get latest player data again
        $this->assertInstanceOf(StreamedViewResponse::class, $response);
        $this->assertGreaterThan($initial_turns-5, $char_2->turns);
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

        $this->assertInstanceOf(StreamedViewResponse::class, $response);
    }

    public function testAttackWhenDead() {
        $attacker = Player::find(SessionFactory::getSession()->get('player_id'));
        $attacker->death();
        $attacker->save();

        $char_id_2 = TestAccountCreateAndDestroy::char_id_2();

        $params = [
            'target' => $char_id_2,
        ];

        $request = Request::create('/attack', 'GET', $params);
        RequestWrapper::inject($request);
        $response = $this->controller->index();

        $this->assertInstanceOf(StreamedViewResponse::class, $response);

        $reflection = new \ReflectionProperty(get_class($response), 'data');
        $reflection->setAccessible(true);
        $response_data = $reflection->getValue($response);

        $this->assertNotEmpty($response_data['error']);
    }
}
