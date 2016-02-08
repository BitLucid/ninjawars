<?php
// Note that the file has to have a file ending of ...test.php to be run by phpunit
require_once(CORE."control/lib_inventory.php");

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use NinjaWars\core\environment\RequestWrapper;
use NinjaWars\core\control\DojoController;
use NinjaWars\core\extensions\SessionFactory;

class DojoControllerTest extends PHPUnit_Framework_TestCase {
    private $controller;

    public function __construct() {
        $this->controller = new DojoController();
    }

    /**
     */
	protected function setUp() {
        // Mock the post request.
        $request = new Request([], []);
        RequestWrapper::inject($request);
		SessionFactory::init(new MockArraySessionStorage());
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
    public function testDojoBuyDimMakDoesNotError() {
        $this->assertNotEmpty($this->controller->buyDimMak());
    }

    /**
     */
    public function testDojoChangeClassDoesNotError() {
        $this->assertNotEmpty($this->controller->changeClass());
    }
}
