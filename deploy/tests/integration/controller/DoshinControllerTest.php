<?php
// Note that the file has to have a file ending of ...test.php to be run by phpunit
require_once(CORE.'control/DoshinController.php');

use Symfony\Component\HttpFoundation\Request;
use app\environment\RequestWrapper;
use app\Controller\DoshinController as DoshinController;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

class DoshinControllerTest extends PHPUnit_Framework_TestCase {
	function setUp() {
        // Mock the post request.
        $request = new Request([], []);
        RequestWrapper::inject($request);
		nw\SessionFactory::init(new MockArraySessionStorage());
	}

	function tearDown() {
        RequestWrapper::inject(new Request([]));
    }

    public function testInstantiateDoshinController() {
        $doshin = new DoshinController();
        $this->assertInstanceOf('app\Controller\DoshinController', $doshin);
    }

    public function testDoshinIndex() {
        $doshin = new DoshinController();
        $output = $doshin->index();
        $this->assertNotEmpty($output);
    }

    public function testDoshinOfferBounty() {
        $doshin = new DoshinController();
        $output = $doshin->offerBounty();
        $this->assertNotEmpty($output);
    }

    public function testBribeCallInDoshinController() {
        $doshin = new DoshinController();
        $output = $doshin->offerBounty();
        $this->assertNotEmpty($output);
    }

    public function testDoshinOfferSomeBountyOnATestPlayer() {
        $this->markTestIncomplete();
    }

    public function testOfferACertainAmountOfBribeOnATestPlayerAfterBountyCreatedForThem() {
        $this->markTestIncomplete();
    }
}
