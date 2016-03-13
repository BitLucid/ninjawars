<?php
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use NinjaWars\core\environment\RequestWrapper;
use NinjaWars\core\control\DojoController;
use NinjaWars\core\data\Player;
use NinjaWars\core\extensions\SessionFactory;

class DojoControllerTest extends PHPUnit_Framework_TestCase {
    private $controller;
    private $char_id;

    public function __construct() {
        $this->controller = new DojoController();
    }

    /**
     */
	protected function setUp() {
        // Mock the post request.
        $request = new Request([], []);
        RequestWrapper::inject($request);
		$this->char_id = TestAccountCreateAndDestroy::create_testing_account();
		$session = SessionFactory::init(new MockArraySessionStorage());
        $session->set('player_id', $this->char_id);
        $session->set('account_id', $this->char_id);
	}

    /**
     */
	protected function tearDown() {
        RequestWrapper::inject(new Request([]));
        $session = SessionFactory::getSession();
        $session->invalidate();
    }

    /**
     */
    public function testDojoControllerCanBeInstantiatedWithoutError() {
        $this->assertInstanceOf('NinjaWars\core\control\DojoController', $this->controller);
    }

    /**
     */
    public function testDojoIndexDoesNotError() {
        $this->assertNotEmpty($this->controller->index());
    }

    /**
     */
    public function testDojoIndexNotLoggedInDoesNotError() {
        $session = SessionFactory::getSession();
        $session->invalidate();
        $this->assertNotEmpty($this->controller->index());
    }

    /**
     */
    public function testDojoBuyDimMakDoesNotError() {
        $this->assertNotEmpty($this->controller->buyDimMak());
    }

    /**
     */
    public function testDojoBuyDimMakWithPostDoesNotError() {
        $request = Request::create('/', 'POST');
        RequestWrapper::inject($request);
        $this->assertNotEmpty($this->controller->buyDimMak());
    }

    /**
     */
    public function testDojoBuyDimMakLowTurnsDoesNotError() {
        $request = Request::create('/', 'POST');
        RequestWrapper::inject($request);
        $char = Player::find($this->char_id);
        $char->setStrength(400);
        $char->set_turns(0);
        $char->save();
        $result = $this->controller->buyDimMak();
        $this->assertNotEmpty($result);
    }
    /**
     */
    public function testDojoBuyDimMakSuccessDoesNotError() {
        $request = Request::create('/', 'POST');
        RequestWrapper::inject($request);
        $char = Player::find($this->char_id);
        $char->setStrength(400);
        $char->set_turns(400);
        $char->save();
        $result = $this->controller->buyDimMak();
        $this->assertNotEmpty($result);
    }

    /**
     */
    public function testDojoBuyDimMakNotLoggedInDoesNotError() {
        $session = SessionFactory::getSession();
        $session->invalidate();
        $this->assertNotEmpty($this->controller->buyDimMak());
    }

    /**
     */
    public function testDojoChangeClassDoesNotError() {
        $this->assertNotEmpty($this->controller->changeClass());
    }
    /**
     */
    public function testDojoChangeClassWithBadClassDoesNotError() {
        $request = Request::create('/', 'GET', ['requested_identity'=>'stupid']);
        RequestWrapper::inject($request);
        $this->assertNotEmpty($this->controller->changeClass());
    }

    /**
     */
    public function testDojoChangeClassWithGoodClassDoesNotError() {
        $request = Request::create('/', 'GET', ['requested_identity'=>'crane']);
        RequestWrapper::inject($request);
        $this->assertNotEmpty($this->controller->changeClass());
    }

    /**
     */
    public function testDojoChangeClassLowTurnsDoesNotError() {
        $request = Request::create('/', 'GET', ['requested_identity'=>'crane']);
        RequestWrapper::inject($request);
        $char = Player::find($this->char_id);
        $char->setStrength(400);
        $char->set_turns(0);
        $char->save();
        $this->assertNotEmpty($this->controller->changeClass());
    }

    /**
     */
    public function testDojoChangeClassNotLoggedInDoesNotError() {
        $session = SessionFactory::getSession();
        $session->invalidate();
        $this->assertNotEmpty($this->controller->changeClass());
    }

}
