<?php
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use NinjaWars\core\environment\RequestWrapper;
use NinjaWars\core\extensions\SessionFactory;
use NinjaWars\core\control\ApiController;

class ApiControllerTest extends PHPUnit_Framework_TestCase {
    const CALLBACK = 'callback';
    private $PAYLOAD_RE;

    private $controller;

    public function __construct() {
        $this->controller = new ApiController();
        $this->PAYLOAD_RE = '/^'.(self::CALLBACK).'\((.*)\)$/';
    }

    public function setUp() {
        // Mock the post request.
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
        $result = $this->controller->nw_json('player', 'illegal!');
        $this->assertEquals(json_encode(false), $result);
    }

    public function testIllegalType() {
        $result = $this->controller->nw_json('illegal', self::CALLBACK);
        $this->assertEquals(null, $result);
    }

    public function testSearch() {
        $request = new Request(['term' => $this->char->uname, 'limit' => 5], []);
        RequestWrapper::inject($request);
        $result = $this->controller->nw_json('char_search', self::CALLBACK);
        $payload = $this->extractPayload($result);

        $this->assertObjectHasAttribute('char_matches', $payload);
        $this->assertCount(1, $payload->char_matches);
        $this->assertObjectHasAttribute('uname', $payload->char_matches[0]);
        $this->assertObjectHasAttribute('player_id', $payload->char_matches[0]);
    }

    public function testChats() {
        $result = $this->controller->nw_json('chats', self::CALLBACK);
        $payload = $this->extractPayload($result);

        $this->assertObjectHasAttribute('chats', $payload);
    }

    public function testLatestChat() {
        $result = $this->controller->nw_json('latest_chat_id', self::CALLBACK);
        $payload = $this->extractPayload($result);

        $this->assertObjectHasAttribute('latest_chat_id', $payload);
    }

    public function testIndex() {
        $result = $this->controller->nw_json('index', self::CALLBACK);
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
        $result = $this->controller->nw_json('player', self::CALLBACK);
        $payload = $this->extractPayload($result);

        $this->assertEquals($payload->player->player_id, $this->char->id());
    }

    public function testLatestEvent() {
        $result = $this->controller->nw_json('latest_event', self::CALLBACK);
        $payload = $this->extractPayload($result);

        $this->assertObjectHasAttribute('event', $payload);
    }

    public function testLatestMessage() {
        $result = $this->controller->nw_json('latest_message', self::CALLBACK);
        $payload = $this->extractPayload($result);

        $this->assertObjectHasAttribute('message', $payload);
    }

    private function extractPayload($p_response) {
        $matches = [];
        preg_match($this->PAYLOAD_RE, $p_response, $matches);
        return json_decode($matches[1]);
    }
}
