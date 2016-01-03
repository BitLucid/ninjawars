<?php
// Note that the file has to have a file ending of ...test.php to be run by phpunit
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use app\environment\RequestWrapper;
use NinjaWars\core\control\DoshinController;
use NinjaWars\core\control\SessionFactory;

class DoshinControllerTest extends PHPUnit_Framework_TestCase {
	function setUp() {
        // Mock the post request.
        $request = new Request([], []);
        RequestWrapper::inject($request);
		SessionFactory::init(new MockArraySessionStorage());
	}

	function tearDown() {
        RequestWrapper::inject(new Request([]));
    }

    public function testInstantiateDoshinController() {
        $doshin = new DoshinController();
        $this->assertInstanceOf('NinjaWars\core\control\DoshinController', $doshin);
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
