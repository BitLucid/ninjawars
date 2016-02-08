<?php
// Note that the file has to have a file ending of ...test.php to be run by phpunit
require_once(CORE."control/lib_inventory.php");

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use NinjaWars\core\environment\RequestWrapper;
use NinjaWars\core\control\ShopController;
use NinjaWars\core\extensions\SessionFactory;

class ShopControllerTest extends PHPUnit_Framework_TestCase {
	function setUp() {
        // Mock the post request.
        $request = new Request([], ['purchase'=>1, 'quantity'=>2, 'item'=>'Shuriken']);
        RequestWrapper::inject($request);
		SessionFactory::init(new MockArraySessionStorage());
	}

	function tearDown() {
        RequestWrapper::inject(new Request([]));
        $session = SessionFactory::getSession();
        $session->invalidate();
    }

    public function testShopControllerCanBeInstantiatedWithoutError() {
        $shop = new ShopController();
        $this->assertInstanceOf('NinjaWars\core\control\ShopController', $shop);
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
