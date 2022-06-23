<?php
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use NinjaWars\core\environment\RequestWrapper;
use NinjaWars\core\extensions\SessionFactory;
use NinjaWars\core\control\ApiController;

class ApiControllerTest extends NWTest {
    const CALLBACK = 'callback';
    private $PAYLOAD_RE;
    private $controller;

    public function setUp():void {
        parent::setUp();
        parent::login();
        // Mock the post request.
        $this->controller = new ApiController();
        $this->PAYLOAD_RE = '/^'.(self::CALLBACK).'\((.*)\)$/';
        $session = SessionFactory::init(new MockArraySessionStorage());
        $this->char = TestAccountCreateAndDestroy::char();
        $session->set('player_id', $this->char->id());
    }

    public function tearDown():void {
        RequestWrapper::inject(new Request([]));
        TestAccountCreateAndDestroy::purge_test_accounts();
        parent::loginTearDown();
        $session = SessionFactory::getSession();
        $session->invalidate();
        parent::tearDown();
    }

    // Want to get the json response out for each controller
    private function extractPayload($p_response, $raw = false)
    {
        if ($raw) {
            return json_decode($p_response->getContent(), true);
        }
        $matches = [];
        preg_match($this->PAYLOAD_RE, $p_response->getContent(), $matches);
        return json_decode($matches[1]);
    }



    public function testIllegalCallbackFails() {
        $request = new Request([
            'type'         => 'player',
            'jsoncallback' => 'illegal!',
        ], []);

        RequestWrapper::inject($request);
        $result = $this->controller->nw_json();
        $this->assertEquals(400, $result->getStatusCode());
        $this->assertEquals('{"error":"Invalid callback"}', $result->getContent());
        TestAccountCreateAndDestroy::purge_test_accounts();
    }

    public function testIllegalTypeShouldGiveNullAnd400StatusCode()
    {
        $request = new Request([
            'type'         => 'illegal',
            'jsoncallback' => self::CALLBACK,
        ], []);

        RequestWrapper::inject($request);
        $result = $this->controller->nw_json();
        $payload = $this->extractPayload($result, true); // Raw json decoded payload
        $this->assertEquals(400, $result->getStatusCode());
        $this->assertNotEmpty($payload['error']);
    }

    public function testSearch() {
        $request = new Request([
            'type'         => 'char_search',
            'jsoncallback' => self::CALLBACK,
            'term'         => $this->char->uname,
            'limit'        => 5,
        ], []);

        RequestWrapper::inject($request);
        $result = $this->controller->nw_json();
        $payload = $this->extractPayload($result);

        $this->assertObjectHasAttribute('char_matches', $payload);
        $this->assertCount(1, $payload->char_matches);
        $this->assertObjectHasAttribute('uname', $payload->char_matches[0]);
        $this->assertObjectHasAttribute('player_id', $payload->char_matches[0]);
    }

    public function testChats() {
        $request = new Request([
            'type'         => 'chats',
            'jsoncallback' => self::CALLBACK,
        ], []);

        RequestWrapper::inject($request);
        $result = $this->controller->nw_json();
        $payload = $this->extractPayload($result);

        $this->assertObjectHasAttribute('chats', $payload);
    }

    public function testLatestChat() {
        $request = new Request([
            'type'         => 'latestChatId',
            'jsoncallback' => self::CALLBACK,
        ], []);

        RequestWrapper::inject($request);
        $result = $this->controller->nw_json();
        $payload = $this->extractPayload($result);
        $this->assertInstanceOf('stdClass', $payload);
        $this->assertObjectHasAttribute('latest_chat_id', $payload);
    }

    public function testIndex() {
        $request = new Request([
            'type'         => 'index',
            'jsoncallback' => self::CALLBACK,
        ], []);

        RequestWrapper::inject($request);
        $result = $this->controller->nw_json();
        $payload = $this->extractPayload($result);

        $this->assertObjectHasAttribute('player', $payload);
        $this->assertObjectHasAttribute('inventory', $payload);
        $this->assertObjectHasAttribute('event', $payload);
        $this->assertObjectHasAttribute('message', $payload);
        $this->assertObjectHasAttribute('member_counts', $payload);
        $this->assertObjectHasAttribute('unread_messages_count', $payload);
        $this->assertObjectHasAttribute('unread_events_count', $payload);
    }

    public function testPlayer() {
        $request = new Request([
            'type'         => 'player',
            'jsoncallback' => self::CALLBACK,
        ], []);

        RequestWrapper::inject($request);
        $result = $this->controller->nw_json();
        $payload = $this->extractPayload($result);

        $this->assertEquals($payload->player->player_id, $this->char->id());
    }

    public function testLatestEvent() {
        $request = new Request([
            'type'         => 'latestEvent',
            'jsoncallback' => self::CALLBACK,
        ], []);

        RequestWrapper::inject($request);
        $result = $this->controller->nw_json();
        $payload = $this->extractPayload($result);
        $this->assertInstanceOf('stdClass', $payload);
        $this->assertObjectHasAttribute('event', $payload);
    }

    public function testLatestMessage() {
        $request = new Request([
            'type'         => 'latestMessage',
            'jsoncallback' => self::CALLBACK,
        ], []);

        RequestWrapper::inject($request);
        $result = $this->controller->nw_json();
        $payload = $this->extractPayload($result);
        $this->assertInstanceOf('stdClass', $payload);
        $this->assertObjectHasAttribute('message', $payload);
    }

    public function testDeactivateCharError()
    {
        $request = new Request([
            'type'         => 'deactivateChar',
            'data'         => '-666',
            'jsoncallback' => self::CALLBACK,
        ], []);

        RequestWrapper::inject($request);
        $result = $this->controller->nw_json();
        $payload = $this->extractPayload($result);

        // There should be no such character to deactivate
        $this->assertObjectHasAttribute('error', $payload);
    }

    public function testReactivateCharError()
    {
        // Can't test much more than this because only admins can reactivate
        $request = new Request(['type'         => 'reactivateChar',
            'data'         => '-666',
            'jsoncallback' => self::CALLBACK,
        ], []);

        RequestWrapper::inject($request);
        $result = $this->controller->nw_json();
        $payload = $this->extractPayload($result);

        // There should be no such character to reactivate
        $this->assertObjectHasAttribute('error', $payload);
    }

    public function testNextTarget()
    {
        $this->markTestSkipped('Not working in ci with fixture data');
        $request = new Request([
            'type'         => 'nextTarget',
            'jsoncallback' => self::CALLBACK,
            'data'         => 0,
        ], []);

        RequestWrapper::inject($request);
        $result = $this->controller->nw_json();
        $payload = $this->extractPayload($result);
        $this->assertNotEmpty($payload, 'No payload returned');
        $this->assertObjectHasAttribute('uname', $payload);
    }

    public function testNextTargetShifted()
    {
        $this->markTestSkipped('Failing in CI but not locally, for some reason.');
        $request = new Request([
            'type'         => 'nextTarget',
            'jsoncallback' => self::CALLBACK,
            'data'        => 0,
        ], []);

        RequestWrapper::inject($request);
        $payload = $this->extractPayload($this->controller->nw_json());
        $this->assertNotEmpty($payload, 'No payload returned');
        $this->assertObjectHasAttribute('uname', $payload, 'No uname returned');
        $first_target = $payload->uname;
        $request2 = new Request([
            'type'         => 'nextTarget',
            'jsoncallback' => self::CALLBACK,
            'data'        => 3,
        ], []);

        RequestWrapper::inject($request2);
        $payload = $this->extractPayload($this->controller->nw_json());
        $this->assertNotEquals($first_target, $payload->uname);
    }


}
