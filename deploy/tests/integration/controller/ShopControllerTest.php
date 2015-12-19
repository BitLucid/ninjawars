<?php
// Note that the file has to have a file ending of ...test.php to be run by phpunit
require_once(CORE.'control/ShopController.php');
require_once(CORE."control/lib_inventory.php");

use Symfony\Component\HttpFoundation\Request;
use app\environment\RequestWrapper;
use app\Controller\ShopController as ShopController;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

class ShopControllerTest extends PHPUnit_Framework_TestCase {
	function setUp() {
        // Mock the post request.
        $request = new Request([], ['purchase'=>1, 'quantity'=>2, 'item'=>'Shuriken']);
        RequestWrapper::inject($request);
		nw\SessionFactory::init(new MockArraySessionStorage());
	}

	function tearDown() {
        RequestWrapper::inject(new Request([]));
    }

    public function testShopControllerCanBeInstantiatedWithoutError() {
        $shop = new ShopController();
        $this->assertInstanceOf('app\Controller\ShopController', $shop);
    }

    public function testShopIndexDoesNotError() {
        $shop = new ShopController();
        $shop_outcome = $shop->index();
        $this->assertNotEmpty($shop_outcome);
    }

    public function testShopPurchaseDoesNotError() {
        // Inject post request.
        $request = new Request([], ['quantity'=>5, 'item'=>'shuriken']);
        RequestWrapper::inject($request);
        $shop = new ShopController();
        $shop_outcome = $shop->buy();
        $this->assertNotEmpty($shop_outcome);
    }

    public function testShopPurchaseHandlesNoItemNoQuantity() {
        // Inject post request.
        RequestWrapper::inject(new Request([], []));
        $shop = new ShopController();
        $shop_outcome = $shop->buy();
        $this->assertNotEmpty($shop_outcome);
    }
}
