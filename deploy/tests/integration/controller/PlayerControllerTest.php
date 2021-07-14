<?php
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use NinjaWars\core\environment\RequestWrapper;
use NinjaWars\core\control\PlayerController;
use NinjaWars\core\extensions\SessionFactory;
use \Pimple\Container;

class PlayerControllerTest extends NWTest {
	public function setUp():void {
        parent::setUp();
        $this->char = TestAccountCreateAndDestroy::char();
        SessionFactory::init(new MockArraySessionStorage());
        $this->m_dependencies = new Container();
	}

	public function tearDown():void {
        TestAccountCreateAndDestroy::destroy();
        RequestWrapper::inject(new Request([]));
        $session = SessionFactory::getSession();
        $session->invalidate();
        $this->m_dependencies = null;
        parent::tearDown();
    }

    public function testPlayerControllerCanBeInstantiatedWithoutError() {
        $player = new PlayerController();
        $this->assertInstanceOf(PlayerController::class, $player);
    }

    public function testPlayerIndexDoesNotErrorOnLoad() {
        $player = new PlayerController();
        $player_outcome = $player->index($this->m_dependencies);
        $this->assertNotEmpty($player_outcome);
    }

    public function testPlayerIndexIsRenderableEventLoggedOut() {
        $player = new PlayerController();
        $player_outcome = $player->index($this->mockLogout());
        $this->assertNotEmpty($player_outcome);
    }

    public function testViewOtherPlayerProfile() {
        $viewing_char_id = TestAccountCreateAndDestroy::char_id_2();
        $request = new Request(['player_id'=>$viewing_char_id]);
        RequestWrapper::inject($request);
        $sess = SessionFactory::getSession();
        $sess->set('player_id', $this->char->id());
        $playerc = new PlayerController();
        $player_outcome = $playerc->index($this->m_dependencies);
        $this->assertNotEmpty($player_outcome);
    }

    public function testViewingOfPlayerProfileMyselfViewingOwnProfile() {
        $viewing_char_id = $this->char->id();
        $request = new Request(['player_id'=>$viewing_char_id]);
        RequestWrapper::inject($request);
        $sess = SessionFactory::getSession();
        $sess->set('player_id', $this->char->id());
        $playerc = new PlayerController();
        $player_outcome = $playerc->index($this->m_dependencies);
        $this->assertNotEmpty($player_outcome);
    }

    public function testThatAPlayerProfileReturnsSomeCombatSkillsToLoopOver() {
        $char_2 = TestAccountCreateAndDestroy::char_2();
        $viewing_char_id = $this->char->id();
        $request = new Request(['player_id'=>$char_2->id()]);
        RequestWrapper::inject($request);
        $sess = SessionFactory::getSession();
        $sess->set('player_id', $this->char->id());
        $this->m_dependencies['current_player'] = $this->char;
        $playerc = new PlayerController();
        $player_outcome = $playerc->index($this->m_dependencies);
        $this->assertNotEmpty($player_outcome);
        // Extract that good good data from in the StreamedViewResponse
        $reflection = new \ReflectionProperty(get_class($player_outcome), 'data');
        $reflection->setAccessible(true);
        $response_data = $reflection->getValue($player_outcome);
        $this->assertNotEmpty($response_data['combat_skills']);
        $this->assertGreaterThan(0, count($response_data['combat_skills']));
        $this->assertGreaterThan(0, count(json_decode($response_data['json_combat_skills'])));
    }
}