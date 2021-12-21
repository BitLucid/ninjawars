<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use NinjaWars\core\environment\RequestWrapper;
use NinjaWars\core\extensions\SessionFactory;
use NinjaWars\core\control\ApiController;
use NinjaWars\core\data\Api;

class ApiTest extends NWTest
{

    public function setUp(): void
    {
        parent::setUp();
        parent::login();
        $session = SessionFactory::init(new MockArraySessionStorage());
        $this->char = TestAccountCreateAndDestroy::char();
        $session->set('player_id', $this->char->id());
    }

    public function tearDown(): void
    {
        RequestWrapper::inject(new Request([]));
        TestAccountCreateAndDestroy::purge_test_accounts();
        parent::loginTearDown();
        $session = SessionFactory::getSession();
        $session->invalidate();
        parent::tearDown();
    }




    public function testNextTargetOfApiReturnsSomething()
    {
        $api = new Api();
        $data = $api->nextTarget($offset = 0);
        $this->assertNotEmpty($data['uname']);
    }
}
