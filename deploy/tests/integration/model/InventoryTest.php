<?php
use NinjaWars\core\data\Inventory;
use NinjaWars\core\data\Player;
use \TestAccountCreateAndDestroy;

class InventoryTest extends PHPUnit_Framework_TestCase {


	public function setUp(){
        $this->char = TestAccountCreateAndDestroy::char();
	}

	public function tearDown(){
        TestAccountCreateAndDestroy::destroy();
	}

    public function testAddShouldIncreaseItemCount(){
        Inventory::add($this->char, 'shuriken', 10);
        $count = query_item('select amount from inventory join item on item.item_id = inventory.item_type where owner = :id', 
                [':id'=>$this->char->id()]
                );
        $this->assertEquals(10, $count);
    }

    public function testInventoryToArrayGetsArrayOfItems(){
        Inventory::add($this->char, 'shuriken', 10);
        $inv = new Inventory($this->char);
        $this->assertNotEmpty($inv->toArray());
    }

    public function testShouldObtainInventory(){
        Inventory::add($this->char, 'shuriken', 10);
        $inv = new Inventory($this->char);
        $shurikens = null;
        foreach($inv as $itemz){
            if($itemz['item_internal_name'] === 'shuriken'){
                $shurikens = $itemz;
            }
        }
        $this->assertEquals($shurikens['count'], 10);
        $this->assertEquals($shurikens['item_internal_name'], 'shuriken');
    }

}
