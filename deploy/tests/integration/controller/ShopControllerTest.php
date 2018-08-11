<?php
use Symfony\Component\HttpFoundation\Request;
use NinjaWars\core\environment\RequestWrapper;
use NinjaWars\core\control\ShopController;
use NinjaWars\core\extensions\SessionFactory;
use NinjaWars\core\data\Inventory;
use NinjaWars\core\data\Player;
use NinjaWars\core\data\Account;

class ShopControllerTest extends NWTest {
	function setUp() {
        // Mock the post request.
        $request = new Request([], ['purchase'=>1, 'quantity'=>2, 'item'=>'Shuriken']);
        RequestWrapper::inject($request);
        $this->login();
	}

	function tearDown() {
        RequestWrapper::inject(new Request([]));
        $this->loginTearDown();
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

    public function testShopIndexRenderableEventLoggedOut() {
        $shop = new ShopController();
        $shop_outcome = $shop->index($this->mockLogout());
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
        $pc = Player::findPlayable($this->account->id());
        $pc->gold = $pc->gold + 999;
        $pc->save();
        $request = new Request([], ['quantity'=>1, 'item'=>'Shuriken']);
        RequestWrapper::inject($request);
        $shop = new ShopController();
        $account_id = $shop->getAccountId();
        $response = $shop->buy();
        $reflection = new \ReflectionProperty(get_class($response), 'data');
        $reflection->setAccessible(true);
        $response_data = $reflection->getValue($response);
        $this->assertNotEmpty($response);
        $this->assertNotEmpty($account_id, 
            'Unable to get accountId from controller/abstract controller');
        $this->assertTrue($response_data['valid']);
        $inv = new Inventory($pc);
        $this->assertEquals(1, $inv->amount('shuriken'));
    }

    public function testShopAllowsPurchasingOfMultipleItems() {
        $pc = Player::findPlayable($this->account->id());
        $pc->gold = $pc->gold + 9999;
        $pc->save();
        $request = new Request([], ['quantity'=>7, 'item'=>'shuriken']);
        RequestWrapper::inject($request);
        $shop = new ShopController();
        $response = $shop->buy();
        $this->assertNotEmpty($response);
        $reflection = new \ReflectionProperty(get_class($response), 'data');
        $reflection->setAccessible(true);
        $response_data = $reflection->getValue($response);
        $this->assertTrue($response_data['valid']);
        $inv = new Inventory($pc);
        $this->assertEquals(7, $inv->amount('shuriken'));
    }

    public function testShopCannotBuyInvalidItem() {
        $pc = Player::findPlayable($this->account->id());
        $pc->gold = $pc->gold + 9999;
        $pc->save();
        $request = new Request([], ['quantity'=>4, 'item'=>'zigzigX']);
        RequestWrapper::inject($request);
        $shop = new ShopController();
        $response = $shop->buy();
        $this->assertNotEmpty($response);
        $reflection = new \ReflectionProperty(get_class($response), 'data');
        $reflection->setAccessible(true);
        $response_data = $reflection->getValue($response);
        $this->assertFalse($response_data['valid']);
    }
}
