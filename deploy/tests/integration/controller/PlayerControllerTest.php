<?php
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use NinjaWars\core\environment\RequestWrapper;
use NinjaWars\core\control\PlayerController;
use NinjaWars\core\extensions\SessionFactory;

class PlayerControllerTest extends PHPUnit_Framework_TestCase {
	protected function setUp() {
        $this->char = TestAccountCreateAndDestroy::char();
		SessionFactory::init(new MockArraySessionStorage());
	}

	protected function tearDown() {
        TestAccountCreateAndDestroy::destroy();
        RequestWrapper::inject(new Request([]));
        $session = SessionFactory::getSession();
        $session->invalidate();
    }

    public function testPlayerControllerCanBeInstantiatedWithoutError() {
        $player = new PlayerController();
        $this->assertInstanceOf('NinjaWars\core\control\PlayerController', $player);
    }

    public function testPlayerIndexDoesNotErrorOnLoad() {
        $player = new PlayerController();
        $player_outcome = $player->index();
        $this->assertNotEmpty($player_outcome);
    }

    public function testViewOtherPlayerProfile() {
        $viewing_char_id = TestAccountCreateAndDestroy::char_id_2();
        $request = new Request(['player_id'=>$viewing_char_id]);
        RequestWrapper::inject($request);
        $sess = SessionFactory::getSession();
        $sess->set('player_id', $this->char->id());
        $player = new PlayerController();
        $player_outcome = $player->index();
        $this->assertNotEmpty($player_outcome);
    }

    public function testViewingOfPlayerProfileMyselfViewingOwnProfile() {
        $viewing_char_id = $this->char->id();
        $request = new Request(['player_id'=>$viewing_char_id]);
        RequestWrapper::inject($request);
        $sess = SessionFactory::getSession();
        $sess->set('player_id', $this->char->id());
        $player = new PlayerController();
        $player_outcome = $player->index();
        $this->assertNotEmpty($player_outcome);
    }
}
