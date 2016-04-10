<?php
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use NinjaWars\core\environment\RequestWrapper;
use NinjaWars\core\extensions\SessionFactory;
use NinjaWars\core\control\ClanController;
use NinjaWars\core\data\Clan;
use NinjaWars\core\data\Player;

class ClanControllerTest extends PHPUnit_Framework_TestCase {
    private $controller;
    private $clan;

    public function __construct() {
        $this->controller = new ClanController();
    }

	protected function setUp() {
		SessionFactory::init(new MockArraySessionStorage());
        $char_id = TestAccountCreateAndDestroy::create_testing_account();
		SessionFactory::getSession()->set('player_id', $char_id);
        $this->clan = Clan::create(Player::find($char_id), 'phpunit_test_clan');
    }

	protected function tearDown() {
        $this->deleteClan($this->clan->id());
        RequestWrapper::destroy();
        TestAccountCreateAndDestroy::purge_test_accounts();
        $session = SessionFactory::getSession();
        $session->invalidate();
    }

    private function deleteClan($clan_id) {
        query('delete from clan where clan_id = :id', [':id'=>$clan_id]);
        query('delete from clan_player where _clan_id = :id', [':id'=>$clan_id]);
    }

    public function testIndex() {
        $response = $this->controller->listClans();

        $this->assertArrayHasKey('template', $response);
    }

    public function testViewMyClan() {
        $request = Request::create('/clan/view', 'GET', ['clan_id'=>$this->clan->id()]);
        RequestWrapper::inject($request);
        $response = $this->controller->view();

        $this->assertArrayHasKey('template', $response);
    }

    public function testViewAnotherClan() {
        // create new character to lead the new clan
        $char_id_2 = TestAccountCreateAndDestroy::char_id_2();

        // create new clan
        $clan = Clan::create(Player::find($char_id_2), 'phpunit_test_clan2');

        // view new clan
        $request = Request::create('/clan/view', 'GET', ['clan_id'=>$clan->id()]);
        RequestWrapper::inject($request);
        $response = $this->controller->view();

        // delete new clan
        $this->deleteClan($clan->id());

        $this->assertArrayHasKey('template', $response);
    }

    public function testViewNonexistentClan() {
        $bad_id = query_item('SELECT max(clan_id)+1 AS bad_id FROM clan');
        $request = Request::create('/clan/view', 'GET', ['clan_id'=>$bad_id]);
        RequestWrapper::inject($request);
        $response = $this->controller->view();

        $this->assertArrayHasKey('template', $response);
    }

    public function testViewNoArgsWithClan() {
        $request = Request::create('/clan/view', 'GET', []);
        RequestWrapper::inject($request);
        $response = $this->controller->view();

        $this->assertArrayHasKey('template', $response);
    }

    public function testViewNoArgsWithoutClan() {
        // create new character, won't have a clan
        $char_id_2 = TestAccountCreateAndDestroy::char_id_2();

        // switch session to new character
		SessionFactory::getSession()->set('player_id', $char_id_2);

        $request = Request::create('/clan/view', 'GET', []);
        RequestWrapper::inject($request);
        $response = $this->controller->view();

        $this->assertArrayHasKey('template', $response);
    }

    public function testViewMyClanWithoutLeadership() {
        // create new character
        $char_id_2 = TestAccountCreateAndDestroy::char_id_2();

        // add new character to clan
        $this->clan->addMember(Player::find($char_id_2), Player::find(SessionFactory::getSession()->get('player_id')));

        // switch session to new character
		SessionFactory::getSession()->set('player_id', $char_id_2);

        // view default clan
        $request = Request::create('/clan/view', 'GET', []);
        RequestWrapper::inject($request);
        $response = $this->controller->view();

        $this->assertArrayHasKey('template', $response);
    }

    public function testInviteAsNotLeader() {
        $this->setExpectedException(\RuntimeException::class);

        // create new character
        $char_id_2 = TestAccountCreateAndDestroy::char_id_2();

        // add new character to clan
        $this->clan->addMember(Player::find($char_id_2), Player::find(SessionFactory::getSession()->get('player_id')));

        // switch session to new character
		SessionFactory::getSession()->set('player_id', $char_id_2);

        // try to invite
        $request = Request::create('/clan/invite', 'GET', []);
        RequestWrapper::inject($request);
        $response = $this->controller->invite();
    }

    public function testInviteWithoutClan() {
        $this->setExpectedException(\RuntimeException::class);

        // create new character
        $char_id_2 = TestAccountCreateAndDestroy::char_id_2();

        // switch session to new character
		SessionFactory::getSession()->set('player_id', $char_id_2);

        // try to invite
        $request = Request::create('/clan/invite', 'GET', []);
        RequestWrapper::inject($request);
        $response = $this->controller->invite();
    }

    public function testInviteAsLeader() {
        // create new character
        $char_id_2 = TestAccountCreateAndDestroy::char_id_2();

        // try to invite
        $request = Request::create('/clan/invite', 'GET', ['person_invited'=>$char_id_2]);
        RequestWrapper::inject($request);
        $response = $this->controller->invite();

        $this->assertArrayHasKey('template', $response);
    }

    public function testInviteNonexistentTarget() {
        // try to invite
        $request = Request::create('/clan/invite', 'GET', ['person_invited'=>-123]);
        RequestWrapper::inject($request);
        $response = $this->controller->invite();

        $this->assertArrayHasKey('template', $response);
        $this->assertEquals($response['parts']['error'], 'Sorry, unable to find a ninja to invite by that name.');
    }

    public function testLeave() {
        // create new character
        $char_id_2 = TestAccountCreateAndDestroy::char_id_2();

        // add new character to clan
        $this->clan->addMember(Player::find($char_id_2), Player::find(SessionFactory::getSession()->get('player_id')));

        // switch session to new character
		SessionFactory::getSession()->set('player_id', $char_id_2);

        // try to leave
        $request = Request::create('/clan/leave', 'GET', []);
        RequestWrapper::inject($request);
        $response = $this->controller->leave();

        $this->assertArrayHasKey('template', $response);
    }

    public function testLeaveAsLeader() {
        $this->setExpectedException(\RuntimeException::class);

        // try to leave
        $request = Request::create('/clan/leave', 'GET', []);
        RequestWrapper::inject($request);
        $response = $this->controller->leave();
    }

    public function testDisbandAsLeaderWithoutConfirm() {
        // try to disband
        $request = Request::create('/clan/disband', 'GET', []);
        RequestWrapper::inject($request);
        $response = $this->controller->disband();

        $this->assertEquals($response['title'], 'Confirm disbanding of your clan');
    }

    public function testDisbandAsLeaderWithConfirm() {
        // try to disband
        $request = Request::create('/clan/disband', 'GET', ['sure'=>'yes']);
        RequestWrapper::inject($request);
        $response = $this->controller->disband();

        $this->assertArrayHasKey('template', $response);
        $this->assertNotEquals($response['title'], 'Confirm disbanding of your clan');
    }

    public function testDisbandAsMember() {
        $this->setExpectedException(\RuntimeException::class);

        // create new character
        $char_id_2 = TestAccountCreateAndDestroy::char_id_2();

        // add new character to clan
        $this->clan->addMember(Player::find($char_id_2), Player::find(SessionFactory::getSession()->get('player_id')));

        // switch session to new character
		SessionFactory::getSession()->set('player_id', $char_id_2);

        // try to disband
        $request = Request::create('/clan/disband', 'GET', []);
        RequestWrapper::inject($request);
        $response = $this->controller->disband();
    }

    public function testKickAsLeader() {
        // create new character
        $char_id_2 = TestAccountCreateAndDestroy::char_id_2();

        // add new character to clan
        $this->clan->addMember(Player::find($char_id_2), Player::find(SessionFactory::getSession()->get('player_id')));

        // try to kick
        $request = Request::create('/clan/kick', 'GET', ['kicked'=>$char_id_2]);
        RequestWrapper::inject($request);
        $response = $this->controller->kick();

        $this->assertArrayHasKey('template', $response);
        $this->assertNotEquals($response['title'], 'Confirm disbanding of your clan');
    }

    public function testKickAsMember() {
        $this->setExpectedException(\RuntimeException::class);

        $char_id_1 = SessionFactory::getSession()->get('player_id');

        // create new character
        $char_id_2 = TestAccountCreateAndDestroy::char_id_2();

        // add new character to clan
        $this->clan->addMember(Player::find($char_id_2), Player::find($char_id_1));

        // switch session to new character
		SessionFactory::getSession()->set('player_id', $char_id_2);

        // try to kick
        $request = Request::create('/clan/kick', 'GET', ['kicked'=>$char_id_1]);
        RequestWrapper::inject($request);
        $response = $this->controller->kick();
    }
}
