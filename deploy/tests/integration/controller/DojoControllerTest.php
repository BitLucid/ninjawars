<?php
// Note that the file has to have a file ending of ...test.php to be run by phpunit
require_once(CORE."control/lib_inventory.php");

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use NinjaWars\core\environment\RequestWrapper;
use NinjaWars\core\control\DojoController;
use NinjaWars\core\control\SessionFactory;

class DojoControllerTest extends PHPUnit_Framework_TestCase {
	function setUp() {
        // Mock the post request.
        $request = new Request([], []);
        RequestWrapper::inject($request);
		SessionFactory::init(new MockArraySessionStorage());
	}

	function tearDown() {
        RequestWrapper::inject(new Request([]));
    }

    public function testDojoControllerCanBeInstantiatedWithoutError() {
        $dojo = new DojoController();
        $this->assertInstanceOf('NinjaWars\core\control\DojoController', $dojo);
    }

    public function testDojoIndexDoesNotError() {
        $dojo = new DojoController();
        $dojo_outcome = $dojo->index();
        $this->assertNotEmpty($dojo_outcome);
    }
}
