<?php
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use NinjaWars\core\environment\RequestWrapper;
use NinjaWars\core\extensions\SessionFactory;
use NinjaWars\core\extensions\StreamedViewResponse;
use NinjaWars\core\control\ClanController;
use NinjaWars\core\data\Clan;
use NinjaWars\core\data\Player;

class ClanControllerTest extends NWTest {
    private $controller;
    private $clan;

	public function setUp() {
        parent::setUp();
        $this->controller = new ClanController();
		SessionFactory::init(new MockArraySessionStorage());
        $char_id = TestAccountCreateAndDestroy::create_testing_account();
		SessionFactory::getSession()->set('player_id', $char_id);
        $this->clan = Clan::create(Player::find($char_id), 'phpunit_test_clan');
    }

	public function tearDown() {
        $this->deleteClan($this->clan->id);
        RequestWrapper::destroy();
        TestAccountCreateAndDestroy::purge_test_accounts();
        $session = SessionFactory::getSession();
        $session->invalidate();
        parent::tearDown();
    }

    private function deleteClan($clan_id) {
        query('delete from clan where clan_id = :id', [':id'=>$clan_id]);
        query('delete from clan_player where _clan_id = :id', [':id'=>$clan_id]);
    }

    public function testIndex() {
        $response = $this->controller->listClans($this->m_dependencies);

        $this->assertInstanceOf(StreamedViewResponse::class, $response);
    }

    public function testIndexRendersEvenIfLoggedOut() {
        $response = $this->controller->listClans($this->mockLogout());

        $this->assertInstanceOf(StreamedViewResponse::class, $response);
    }

    public function testViewMyClan() {
        $request = Request::create('/clan/view', 'GET', ['clan_id'=>$this->clan->id]);
        RequestWrapper::inject($request);
        $response = $this->controller->view($this->m_dependencies);

        $this->assertInstanceOf(StreamedViewResponse::class, $response);
    }

    public function testViewAnotherClan() {
        // create new character to lead the new clan
        $char_id_2 = TestAccountCreateAndDestroy::char_id_2();

        // create new clan
        $clan = Clan::create(Player::find($char_id_2), 'phpunit_test_clan2');

        // view new clan
        $request = Request::create('/clan/view', 'GET', ['clan_id'=>$clan->id]);
        RequestWrapper::inject($request);
        $response = $this->controller->view($this->m_dependencies);

        // delete new clan
        $this->deleteClan($clan->id);

        $this->assertInstanceOf(StreamedViewResponse::class, $response);
    }

    public function testViewNonexistentClan() {
        $bad_id = query_item('SELECT max(clan_id)+1 AS bad_id FROM clan');
        $request = Request::create('/clan/view', 'GET', ['clan_id'=>$bad_id]);
        RequestWrapper::inject($request);
        $response = $this->controller->view($this->m_dependencies);

        $this->assertInstanceOf(StreamedViewResponse::class, $response);
    }

    public function testViewNoArgsWithClan() {
        $request = Request::create('/clan/view', 'GET', []);
        RequestWrapper::inject($request);
        $response = $this->controller->view($this->m_dependencies);

        $this->assertInstanceOf(StreamedViewResponse::class, $response);
    }

    public function testViewNoArgsWithoutClan() {
        // create new character, won't have a clan
        $char_id_2 = TestAccountCreateAndDestroy::char_id_2();

        // switch session to new character
		$this->m_dependencies['session']->set('player_id', $char_id_2);

        $request = Request::create('/clan/view', 'GET', []);
        RequestWrapper::inject($request);
        $response = $this->controller->view($this->m_dependencies);

        $this->assertInstanceOf(StreamedViewResponse::class, $response);
    }

    public function testViewMyClanWithoutLeadership() {
        // create new character
        $char_id_2 = TestAccountCreateAndDestroy::char_id_2();

        // add new character to clan
        $this->clan->addMember(Player::find($char_id_2), $this->m_dependencies['current_player']);

        // switch session to new character
		$this->m_dependencies['session']->set('player_id', $char_id_2);

        // view default clan
        $request = Request::create('/clan/view', 'GET', []);
        RequestWrapper::inject($request);
        $response = $this->controller->view($this->m_dependencies);

        $this->assertInstanceOf(StreamedViewResponse::class, $response);
    }

    public function testInviteAsNotLeader() {
        $this->expectException(\RuntimeException::class);

        // create new character
        $char_id_2 = TestAccountCreateAndDestroy::char_id_2();

        // add new character to clan
        $this->clan->addMember(Player::find($char_id_2), $this->m_dependencies['current_player']);

        // switch session to new character
        $this->m_dependencies['session']->set('player_id', $char_id_2);

        // try to invite
        $request = Request::create('/clan/invite', 'GET', []);
        RequestWrapper::inject($request);
        $response = $this->controller->invite($this->m_dependencies);
    }

    public function testInviteWithoutClan() {
        $this->expectException(\RuntimeException::class);

        // create new character
        $char_id_2 = TestAccountCreateAndDestroy::char_id_2();

        // switch session to new character
		$this->m_dependencies['session']->set('player_id', $char_id_2);

        // try to invite
        $request = Request::create('/clan/invite', 'GET', []);
        RequestWrapper::inject($request);
        $response = $this->controller->invite($this->m_dependencies);
    }

    public function testInviteAsLeader() {
        // create new character
        $char_id_2 = TestAccountCreateAndDestroy::char_id_2();

        // try to invite
        $request = Request::create('/clan/invite', 'GET', ['person_invited'=>$char_id_2]);
        RequestWrapper::inject($request);
        $response = $this->controller->invite($this->m_dependencies);

        $this->assertInstanceOf(StreamedViewResponse::class, $response);
    }

    public function testInviteNonexistentTarget() {
        // try to invite
        $request = Request::create('/clan/invite', 'GET', ['person_invited'=>-123]);
        RequestWrapper::inject($request);
        $response = $this->controller->invite($this->m_dependencies);

        $this->assertInstanceOf(StreamedViewResponse::class, $response);
        $reflection = new \ReflectionProperty(get_class($response), 'data');
        $reflection->setAccessible(true);
        $response_data = $reflection->getValue($response);
        $this->assertEquals($response_data['error'], 'Sorry, unable to find a ninja to invite by that name.');
    }

    public function testJoin() {
        // create new character
        $char_id_2 = TestAccountCreateAndDestroy::char_id_2();

        // switch session to new character
        $this->m_dependencies['session']->set('player_id', $char_id_2);

        // try to leave
        $clan_id = query('select clan_id from clan limit 1');
        $request = Request::create('/clan/join', 'GET', ['clan_id'=>1]);
        RequestWrapper::inject($request);
        $response = $this->controller->join($this->m_dependencies);

        $this->assertInstanceOf(StreamedViewResponse::class, $response);
    }

    public function testLeave() {
        // create new character
        $char_id_2 = TestAccountCreateAndDestroy::char_id_2();

        // add new character to clan
        $this->clan->addMember(Player::find($char_id_2), $this->m_dependencies['current_player']);

        // switch session to new character
		$this->m_dependencies['session']->set('player_id', $char_id_2);



        // try to leave
        $request = Request::create('/clan/leave', 'GET', []);
        RequestWrapper::inject($request);
        $response = $this->controller->leave($this->m_dependencies);

        $this->assertInstanceOf(StreamedViewResponse::class, $response);
    }

    public function testLeaveAsLeader() {
        $this->expectException(\RuntimeException::class);

        // try to leave
        $request = Request::create('/clan/leave', 'GET', []);
        RequestWrapper::inject($request);
        $response = $this->controller->leave($this->m_dependencies);
    }

    public function testDisbandAsLeaderWithoutConfirm() {
        // try to disband
        $request = Request::create('/clan/disband', 'GET', []);
        RequestWrapper::inject($request);
        $response = $this->controller->disband($this->m_dependencies);

        $reflection = new \ReflectionProperty(get_class($response), 'title');
        $reflection->setAccessible(true);
        $response_title = $reflection->getValue($response);
        $this->assertEquals($response_title, 'Confirm disbanding of your clan');
    }

    public function testDisbandAsLeaderWithConfirm() {
        // try to disband
        $request = Request::create('/clan/disband', 'GET', ['sure'=>'yes']);
        RequestWrapper::inject($request);
        $response = $this->controller->disband($this->m_dependencies);

        $this->assertInstanceOf(StreamedViewResponse::class, $response);
        $reflection = new \ReflectionProperty(get_class($response), 'title');
        $reflection->setAccessible(true);
        $response_title = $reflection->getValue($response);
        $this->assertNotEquals($response_title, 'Confirm disbanding of your clan');
    }

    public function testDisbandAsMember() {
        $this->expectException(\RuntimeException::class);

        // create new character
        $char_id_2 = TestAccountCreateAndDestroy::char_id_2();

        // add new character to clan
        $this->clan->addMember(Player::find($char_id_2), $this->m_dependencies['current_player']);

        // switch session to new character
		$this->m_dependencies['session']->set('player_id', $char_id_2);

        // try to disband
        $request = Request::create('/clan/disband', 'GET', []);
        RequestWrapper::inject($request);
        $response = $this->controller->disband($this->m_dependencies);
    }

    public function testKickAsLeader() {
        // create new character
        $char_id_2 = TestAccountCreateAndDestroy::char_id_2();

        // add new character to clan
        $this->clan->addMember(Player::find($char_id_2), $this->m_dependencies['current_player']);

        // try to kick
        $request = Request::create('/clan/kick', 'GET', ['kicked'=>$char_id_2]);
        RequestWrapper::inject($request);
        $response = $this->controller->kick($this->m_dependencies);

        $this->assertInstanceOf(StreamedViewResponse::class, $response);
        $reflection = new \ReflectionProperty(get_class($response), 'title');
        $reflection->setAccessible(true);
        $response_title = $reflection->getValue($response);
        $this->assertNotEquals($response_title, 'Confirm disbanding of your clan');
    }

    public function testKickAsMember() {
        $this->expectException(\RuntimeException::class);

        $char_id_1 = $this->m_dependencies['session']->get('player_id');

        // create new character
        $char_id_2 = TestAccountCreateAndDestroy::char_id_2();

        // add new character to clan
        $this->clan->addMember(Player::find($char_id_2), Player::find($char_id_1));

        // switch session to new character
		$this->m_dependencies['session']->set('player_id', $char_id_2);

        // try to kick
        $request = Request::create('/clan/kick', 'GET', ['kicked'=>$char_id_1]);
        RequestWrapper::inject($request);
        $response = $this->controller->kick($this->m_dependencies);
    }
}
