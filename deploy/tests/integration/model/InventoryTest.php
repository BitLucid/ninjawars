<?php
namespace NinjaWars\tests\integration\model;

use NinjaWars\core\data\Inventory;
use NinjaWars\core\data\Player;
use \TestAccountCreateAndDestroy as TestAccountCreateAndDestroy;

class InventoryTest extends \NWTest {
    
    public function setUp():void {
        parent::setUp();
        TestAccountCreateAndDestroy::destroy();
        $this->char = TestAccountCreateAndDestroy::char();
    }

    public function tearDown():void {
        TestAccountCreateAndDestroy::destroy();
        parent::tearDown();
    }

    /**
     * @group Inventory
     */
    public function testAddShouldIncreaseItemCount() {
        $inventory = new Inventory($this->char);
        $inventory->add('shuriken', 10);

        $count = query_item(
            'select amount from inventory join item on item.item_id = inventory.item_type where owner = :id',
            [':id'=>$this->char->id()]

        );
        $this->assertEquals(10, $count);
    }

    /**
     * @group Inventory
     */
    public function testInventoryToArrayGetsArrayOfItems() {
        $inventory = new Inventory($this->char);
        $inventory->add('shuriken', 10);
        $inventory->add('amanita', 40);
        $this->assertNotEmpty($inventory->toArray());
    }

    /**
     * @group Inventory
     */
    public function testInventorySortBySelfUse() {
        $inventory = new Inventory($this->char);
        $inventory->add('shuriken', 10);
        $inventory->add('amanita', 40);
        $sorted_inv = Inventory::of($this->char, $sort='self');

        // Foreach over the inventory to get the first item out.
        foreach ($sorted_inv as $item) {
            break;
        }

        $this->assertEquals($item['name'], 'Amanita Mushroom');
    }

    /**
     * @group Inventory
     */
    public function testShouldObtainInventory() {
        $inventory = new Inventory($this->char);
        $inventory->add('shuriken', 10);
        $shurikens = null;

        foreach ($inventory as $itemz) {
            if ($itemz['item_internal_name'] === 'shuriken') {
                $shurikens = $itemz;
            }
        }

        $this->assertEquals($shurikens['count'], 10);
        $this->assertEquals($shurikens['item_internal_name'], 'shuriken');
    }
}
