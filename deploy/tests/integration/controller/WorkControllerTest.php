<?php
// Note that the file has to have a file ending of ...test.php to be run by phpunit
require_once(CORE."control/lib_inventory.php");

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use NinjaWars\core\environment\RequestWrapper;
use NinjaWars\core\control\WorkController;
use NinjaWars\core\extensions\SessionFactory;

class WorkControllerTest extends PHPUnit_Framework_TestCase {
	function setUp() {
        // Mock the post request.
        $request = new Request([], ['worked'=>10]);
        RequestWrapper::inject($request);
        SessionFactory::init(new MockArraySessionStorage());
	}

	function tearDown() {
        RequestWrapper::inject(new Request([]));
        $session = SessionFactory::getSession();
        $session->invalidate();
        TestAccountCreateAndDestroy::destroy();
    }

    public function testWorkControllerCanBeInstantiatedWithoutError() {
        $cont = new WorkController();
        $this->assertInstanceOf('NinjaWars\core\control\WorkController', $cont);
    }

    public function testWorkIndexDoesNotError() {
        $work = new WorkController();
        $work_response = $work->index();
        $this->assertNotEmpty($work_response);
    }

    public function testLargeWorkRequestWithoutEnoughTurnsIsRejected() {
        $this->char = TestAccountCreateAndDestroy::char();
        SessionFactory::getSession()->set('player_id', $this->char->id());
        $request = new Request([], ['worked'=>999]);
        RequestWrapper::inject($request);
        $work = new WorkController();
        $work_response = $work->requestWork();
        $earned_gold = $work_response['parts']['earned_gold'];
        $this->assertTrue($work_response['parts']['not_enough_energy']);
        $this->assertEquals('0', $earned_gold);
    }

    public function testCapTurnsPossibleToWorkFor() {
        $this->char = TestAccountCreateAndDestroy::char();
        SessionFactory::getSession()->set('player_id', $this->char->id());
        $request = new Request([], ['worked'=>99977777]);
        RequestWrapper::inject($request);
        $work = new WorkController();
        $work_response = $work->requestWork();
        $earned_gold = $work_response['parts']['earned_gold'];
        $this->assertTrue($work_response['parts']['not_enough_energy']);
        $this->assertEquals('0', $earned_gold);
    }

    public function testWorkDoesNothingWithNegativeWorkRequest(){
        // Note that this had to have an active logged in character to not just get an ignored result of "0" gold.
        $this->char = TestAccountCreateAndDestroy::char();
        SessionFactory::getSession()->set('player_id', $this->char->id());
        $request = new Request([], ['worked'=>-999]);
        RequestWrapper::inject($request);
        $work = new WorkController();
        $work_response = $work->requestWork();
        $earned_gold = $work_response['parts']['earned_gold'];
        $this->assertEquals("0", $earned_gold);
    }
}
