<?php
// Note that the file has to have a file ending of ...test.php to be run by phpunit
require_once(CORE.'control/ShopController.php');
require_once(CORE."control/lib_inventory.php");
use Symfony\Component\HttpFoundation\Request;
use app\environment\RequestWrapper;
use app\Controller\ShopController as ShopController;


class TestShopController extends PHPUnit_Framework_TestCase {


	function setUp(){
        // Mock the post request.
        $request = new Request($get=[], $post=['purchase'=>1, 'quantity'=>2, 'item'=>'Shuriken']);
        RequestWrapper::inject($request);
	}
	
	function tearDown(){
        RequestWrapper::inject(new Request([]));
    }

    public function testShopControllerCanBeInstantiatedWithoutError(){
        $shop = new ShopController();
        $this->assertInstanceOf('app\Controller\ShopController', $shop);
    }

    public function testShopIndexDoesNotError(){
        $shop = new ShopController();
        ob_start();
        $shop->index();
        $shop_outcome = ob_get_contents();
        ob_end_clean();
        $this->assertNotEmpty($shop_outcome);
    }

    public function testShopPurchaseDoesNotError(){
        // Inject post request.
        $request = new Request([], ['quantity'=>5, 'item'=>'shuriken']);
        RequestWrapper::inject($request);
        $shop = new ShopController();
        ob_start();
        $shop->buy();
        $shop_outcome = ob_get_contents();
        ob_end_clean();
        $this->assertNotEmpty($shop_outcome);
    }

    public function testShopPurchaseHandlesNoItemNoQuantity(){
        // Inject post request.
        $request = new Request([], []);
        RequestWrapper::inject($request);
        $shop = new ShopController();
        ob_start();
        $shop->buy();
        $shop_outcome = ob_get_contents();
        ob_end_clean();
        $this->assertNotEmpty($shop_outcome);
    }


}

