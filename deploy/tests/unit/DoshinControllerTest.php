<?php
// Note that the file has to have a file ending of ...test.php to be run by phpunit
require_once(CORE.'control/DoshinController.php');
use Symfony\Component\HttpFoundation\Request;
use app\environment\RequestWrapper;
use app\Controller\DoshinController as DoshinController;


class TestShopController extends PHPUnit_Framework_TestCase {


	function setUp(){
        // Mock the post request.
        $request = new Request($get=[], $post=['purchase'=>1, 'quantity'=>2, 'item'=>'Shuriken']);
        RequestWrapper::inject($request);
        SESSION::inject([]); // Just initialize as an injected session, with no data at the moment, no mock login
	}
	
	function tearDown(){
        SESSION::destroy();
    }

    public function testInstantiateShopController(){
        $doshin = new DoshinController();
        $this->assertInstanceOf('app\Controller\DoshinController', $doshin);
    }

    public function testDoshinIndex(){
        $doshin = new DoshinController();
        ob_start();
        $doshin->index();
        $output = ob_get_contents();
        ob_end_clean();
        $this->assertNotEmpty($output);
        $this->assertTrue(stristr($output, 'Doshin Office') !== false, 'Doshin Office not found in doshin page');
    }

    public function testDoshinOfferBounty(){
        $doshin = new DoshinController();
        ob_start();
        $doshin->offerBounty();
        $output = ob_get_contents();
        ob_end_clean();
        $this->assertNotEmpty($output);
    }

    public function testBribeCallInDoshinController(){
        $doshin = new DoshinController();
        ob_start();
        $doshin->offerBounty();
        $output = ob_get_contents();
        ob_end_clean();
        $this->assertNotEmpty($output);
    }

    public function testDoshinOfferSomeBountyOnATestPlayer(){
        $this->markTestIncomplete();
    }

    public function testOfferACertainAmountOfBribeOnATestPlayerAfterBountyCreatedForThem(){
        $this->markTestIncomplete();
    }

}

