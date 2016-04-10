<?php
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use NinjaWars\core\environment\RequestWrapper;
use NinjaWars\core\control\ShopController;
use NinjaWars\core\extensions\SessionFactory;
use NinjaWars\core\data\Inventory;
use NinjaWars\core\data\Player;

class ShopControllerTest extends PHPUnit_Framework_TestCase {
	function setUp() {
        // Mock the post request.
        $request = new Request([], ['purchase'=>1, 'quantity'=>2, 'item'=>'Shuriken']);
        RequestWrapper::inject($request);
		SessionFactory::init(new MockArraySessionStorage());
        SessionFactory::getSession()->set('authenticated', true);
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

    public function testShopAllowsPurchasingOfItems() {
        $char_id = TestAccountCreateAndDestroy::char_id();
        SessionFactory::getSession()->set('player_id', $char_id);
        $pc = Player::find($char_id);
        $pc->gold = $pc->gold + 999;
        $pc->save();
        $request = new Request([], ['quantity'=>1, 'item'=>'shuriken']);
        RequestWrapper::inject($request);
        $shop = new ShopController();
        $shop_outcome = $shop->buy();
        $this->assertNotEmpty($shop_outcome);
        $this->assertTrue($shop_outcome['parts']['valid']);
        $inv = new Inventory($pc);
        $this->assertEquals(1, $inv->amount('shuriken'));
    }

    public function testShopAllowsPurchasingOfMultipleItems() {
        $char_id = TestAccountCreateAndDestroy::char_id();
        SessionFactory::getSession()->set('player_id', $char_id);
        $pc = Player::find($char_id);
        $pc->gold = $pc->gold + 9999;
        $pc->save();
        $request = new Request([], ['quantity'=>7, 'item'=>'shuriken']);
        RequestWrapper::inject($request);
        $shop = new ShopController();
        $shop_outcome = $shop->buy();
        $this->assertNotEmpty($shop_outcome);
        $this->assertTrue($shop_outcome['parts']['valid']);
        $inv = new Inventory($pc);
        $this->assertEquals(7, $inv->amount('shuriken'));
    }

    public function testShopCannotBuyInvalidItem() {
        $char_id = TestAccountCreateAndDestroy::char_id();
        SessionFactory::getSession()->set('player_id', $char_id);
        $pc = Player::find($char_id);
        $pc->gold = $pc->gold + 9999;
        $pc->save();
        $request = new Request([], ['quantity'=>4, 'item'=>'zigzigX']);
        RequestWrapper::inject($request);
        $shop = new ShopController();
        $shop_outcome = $shop->buy();
        $this->assertNotEmpty($shop_outcome);
        $this->assertFalse($shop_outcome['parts']['valid']);
    }
}