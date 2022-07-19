<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use NinjaWars\core\environment\RequestWrapper;
use NinjaWars\core\extensions\SessionFactory;
use NinjaWars\core\control\ApiController;
use NinjaWars\core\data\Api;

class ApiTest extends NWTest {
    public function setUp(): void {
        parent::setUp();
        parent::login();
        $session = SessionFactory::init(new MockArraySessionStorage());
        $this->char = TestAccountCreateAndDestroy::char();
        $this->char2 = TestAccountCreateAndDestroy::char_2();
        $session->set('player_id', $this->char->id());
    }

    public function tearDown(): void {
        RequestWrapper::inject(new Request([]));
        TestAccountCreateAndDestroy::purge_test_accounts();
        parent::loginTearDown();
        $session = SessionFactory::getSession();
        $session->invalidate();
        parent::tearDown();
    }

    public function testNextTargetOfApiReturnsSomething() {
        $api = new Api();
        $data = $api->nextTarget($offset = 0);
        $this->assertNotEmpty($data, 'Api::nextTarget() returned empty data');
        $this->assertNotEmpty($data['uname'], 'Api::nextTarget() returned empty uname');
    }

    public function testCharSearchCanRun() {
        $api = new Api();
        $data = $api->charSearch('phpunit', $limit = 1);
        $this->assertNotEmpty($data, 'Api::charSearch() returned empty data');
        $this->assertNotEmpty(reset($data)[0]['uname'], 'Api::charSearch() returned empty uname');
    }

    public function testCharDeactivateRunsAtAll() {
        $api = new Api();
        $data = $api->deactivateChar($this->char2->id());
        $this->assertNotEmpty($data, 'Api::deactivateChar() returned empty data');
        // Will actually error because not admin, but that's fine
    }

    public function testCharActivateRunsAtAll() {
        $api = new Api();
        $data = $api->reactivateChar($this->char2->id());
        $this->assertNotEmpty($data, 'Api::deactivateChar() returned empty data');
        // Will actually error because not admin, but that's fine
    }
}
