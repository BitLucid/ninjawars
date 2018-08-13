<?php
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\HttpFoundation\RedirectResponse;
use NinjaWars\core\environment\RequestWrapper;
use NinjaWars\core\control\NewsController;
use NinjaWars\core\extensions\SessionFactory;
use NinjaWars\core\data\Player;
use \TestAccountCreateAndDestroy as TestAccountCreateAndDestroy;

class NewsControllerTest extends NWTest {
	public function setUp() {
        $this->char = Player::find(TestAccountCreateAndDestroy::char_id());
        SessionFactory::init(new MockArraySessionStorage());
        $session = SessionFactory::getSession();
        $session->set('player_id', $this->char->id()); // Mock the login.

        $request = new Request([], []);
        RequestWrapper::inject($request);
	}

	public function tearDown() {
        $this->char = null;
        TestAccountCreateAndDestroy::destroy();
        RequestWrapper::inject(new Request([]));
        $session = SessionFactory::getSession();
        $session->invalidate();
    }

    public function testIndexLoadsTitle(){
        $cont = new NewsController();
        $response = $cont->index();
        $this->assertNotEmpty($response);
        $reflection = new \ReflectionProperty(get_class($response), 'title');
        $reflection->setAccessible(true);
        $response_title = $reflection->getValue($response);
        $this->assertEquals('News Board', $response_title);
    }

    public function testCreateRedirectsForNonAdmin(){
        $cont = new NewsController();
        $res = $cont->create();
        $this->assertInstanceOf(RedirectResponse::class, $res);
    }

    public function testCreateLoadsForAdminPlayer(){
        $this->markTestIncomplete();
        $this->char->uname = 'Tchalvak'; // HARDCODED STRING HACK

        $this->char->save();
        $session->set('player_id', $this->char->id()); // Mock the login.
        $this->assertTrue($this->char->isAdmin());
        $cont = new NewsController();
        $response = $cont->create();
        $this->assertNotEmpty($response);
        $this->assertNotInstanceOf(RedirectResponse::class, $response);
        $reflection = new \ReflectionProperty(get_class($response), 'title');
        $reflection->setAccessible(true);
        $response_title = $reflection->getValue($response);
        $this->assertEquals('Make New Post', $response_title);
    }

    public function testStoreRedirectsForBlankRequest(){
        $cont = new NewsController();
        $res = $cont->store();
        $this->assertInstanceOf(RedirectResponse::class, $res);
        $this->assertTrue(false !== mb_stripos($res->getTargetUrl(), '/news/')); // Check redirects to /news/
    }
}
