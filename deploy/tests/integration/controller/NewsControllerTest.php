<?php
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\HttpFoundation\RedirectResponse;
use NinjaWars\core\environment\RequestWrapper;
use NinjaWars\core\control\NewsController;
use NinjaWars\core\extensions\SessionFactory;
use \TestAccountCreateAndDestroy as TestAccountCreateAndDestroy;
use \Player as Player;

class NewsControllerTest extends \PHPUnit_Framework_TestCase {
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
        $res = $cont->index();
        $this->assertNotEmpty($res);
        $this->assertEquals('News Board', $res['title']);
    }

    public function testCreateRedirectsForNonAdmin(){
        $cont = new NewsController();
        $res = $cont->create();
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\RedirectResponse', $res);
    }

    public function testCreateLoadsForAdminPlayer(){
        $this->markTestIncomplete();
        $this->char->vo->uname = 'Tchalvak'; // HARDCODED STRING HACK

        $this->char->save();
        $session->set('player_id', $this->char->id()); // Mock the login.
        $this->assertTrue($this->char->isAdmin());
        $cont = new NewsController();
        $res = $cont->create();
        $this->assertNotEmpty($res);
        $this->assertNotInstanceOf('Symfony\Component\HttpFoundation\RedirectResponse', $res);
        $this->assertEquals('Make New Post', $res['parts']['title']);
    }

    public function testStoreRedirectsForBlankRequest(){
        $cont = new NewsController();
        $res = $cont->store();
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\RedirectResponse', $res);
        $this->assertTrue(false !== mb_stripos($res->getTargetUrl(), '/news/')); // Check redirects to /news/
    }
}
