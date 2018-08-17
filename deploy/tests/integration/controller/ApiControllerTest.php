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

    public function setUp() {
        // Mock the post request.
        $this->controller = new ApiController();
        $this->PAYLOAD_RE = '/^'.(self::CALLBACK).'\((.*)\)$/';
        $session = SessionFactory::init(new MockArraySessionStorage());
        $this->char = TestAccountCreateAndDestroy::char();
        $session->set('player_id', $this->char->id());
    }

    public function tearDown() {
        RequestWrapper::inject(new Request([]));
        TestAccountCreateAndDestroy::purge_test_accounts();
        $session = SessionFactory::getSession();
        $session->invalidate();
    }

    public function testIllegalCallbackFails() {
        $request = new Request([
            'type'         => 'player',
            'jsoncallback' => 'illegal!',
        ], []);

        RequestWrapper::inject($request);
        $result = $this->controller->nw_json();
        $this->assertEquals(json_encode(false), $result->getContent());
    }

    public function testIllegalType() {
        $request = new Request([
            'type'         => 'illegal',
            'jsoncallback' => self::CALLBACK,
        ], []);

        RequestWrapper::inject($request);
        $result = $this->controller->nw_json();
        $this->assertEquals('', $result->getContent());
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
            'type'         => 'latest_chat_id',
            'jsoncallback' => self::CALLBACK,
        ], []);

        RequestWrapper::inject($request);
        $result = $this->controller->nw_json();
        $payload = $this->extractPayload($result);

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
            'type'         => 'latest_event',
            'jsoncallback' => self::CALLBACK,
        ], []);

        RequestWrapper::inject($request);
        $result = $this->controller->nw_json();
        $payload = $this->extractPayload($result);

        $this->assertObjectHasAttribute('event', $payload);
    }

    public function testLatestMessage() {
        $request = new Request([
            'type'         => 'latest_message',
            'jsoncallback' => self::CALLBACK,
        ], []);

        RequestWrapper::inject($request);
        $result = $this->controller->nw_json();
        $payload = $this->extractPayload($result);

        $this->assertObjectHasAttribute('message', $payload);
    }

    private function extractPayload($p_response) {
        $matches = [];
        preg_match($this->PAYLOAD_RE, $p_response->getContent(), $matches);
        return json_decode($matches[1]);
    }
}
