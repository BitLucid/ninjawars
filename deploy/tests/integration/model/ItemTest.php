<?php
use NinjaWars\core\data\Item;
use NinjaWars\core\data\Player;

class ItemTest extends PHPUnit_Framework_TestCase {


	public function setUp()
	{
	}

	public function tearDown()
	{
	}

	public function testInstantiatingABlankItem(){
		$item = new Item();
		$this->assertTrue($item instanceof Item);
	}

    public function testAnyItemAtAllExists(){
        $identity = query_item('select item_internal_name from item limit 1');
        $item = Item::findByIdentity($identity);
        $this->assertTrue($item instanceof Item);
    }

	public function testSomeItemsExist(){
        /*
        $lantern = Item::findByIdentity('lantern');
		$shuriken = Item::findByIdentity('shuriken');
		$kunai = Item::findByIdentity('kunai');
		$tessen = Item::findByIdentity('tessen');
        */
        $items = ['shuriken', 'kunai', 'tessen', 'lantern'];
        foreach($items as $identity){
            $this->assertNotEmpty(Item::findByIdentity($identity), 'Item ['.$identity.'] was not able to be found or instantiated.');
        }
	}

	public function testRetrievingAShuriken(){
		$shuriken = Item::findByIdentity('shuriken');
		$this->assertTrue($shuriken instanceof Item);
		$this->assertEquals('shuriken', $shuriken->identity());
	}

	public function testTessenAndKunaiHaveSomeMaxDamage(){
		$tessen = Item::findByIdentity('tessen');
        $this->assertNotEmpty($tessen->identity());
		$this->assertGreaterThan(0, $tessen->getMaxDamage());
		$kunai = Item::findByIdentity('kunai');
		$this->assertGreaterThan(0, $kunai->getMaxDamage());
	}

	// Random damage check helper function
	public function itemRandomDamageSum($item, $iterations=1000){
		$sum = 0;
		$i = $iterations;
		while($i > 0){
			$sum += $item->getRandomDamage();
			$i--;
		}
		return $sum;
	}

	public function testKunaiHasSomeRandomDamage(){
		$kunai = Item::findByIdentity('kunai');
		$sum = $this->itemRandomDamageSum($kunai);
		$this->assertGreaterThan(0, $sum);
	}

	public function testWeaponsHaveSomeRandomDamage(){
		// Special cases:  Shuriken...
		$this->assertGreaterThan(0, $this->itemRandomDamageSum(Item::findByIdentity('kunai')));
		$this->assertGreaterThan(0, $this->itemRandomDamageSum(Item::findByIdentity('ono')));
		$this->assertGreaterThan(0, $this->itemRandomDamageSum(Item::findByIdentity('bo')));
		$this->assertGreaterThan(0, $this->itemRandomDamageSum(Item::findByIdentity('kama')));
		$this->assertGreaterThan(0, $this->itemRandomDamageSum(Item::findByIdentity('tetsubo')));
	}

    public function testItemThatExistsHasATypeAndIdentity(){
        $item = Item::findByIdentity('shuriken');
        $this->assertEquals('shuriken', $item->identity());
        $this->assertGreaterThan(0, $item->getType());
    }

    public function testThatAShurikenHasSliceEffect(){
        $item = Item::findByIdentity('shuriken');
        $this->assertGreaterThan(0, $item->hasEffect('slice'));
    }

    public function testThatAShurikenIsUsableOnOthers(){
        $item = Item::findByIdentity('shuriken');
        $this->assertTrue($item->isOtherUsable());
    }

    public function testThatShurikenDoesNotIgnoreStealth(){
        $item = Item::findByIdentity('shuriken');
        $this->assertFalse($item->ignoresStealth());
    }

    public function testThatShurikensArentSelfUsable(){
        $item = Item::findByIdentity('shuriken');
        $this->assertFalse($item->isSelfUsable());
    }

    public function testThatCaltropsIdeallyCauseNegativeTurnChange(){
        $item = Item::findByIdentity('caltrops');
        $this->assertLessThan(0, $item->getMaxTurnChange());
    }

    public function testBuffItemsIgnoreStealth(){
        foreach(['mirror', 'prayerwheel', 'shell', 'lantern'] as $ident){
            $item = Item::findByIdentity($ident);
            $this->assertTrue($item->ignoresStealth());
        }
    }

    public function testMeitoNamedKatanaIgnoresStealth(){
        $item = Item::findByIdentity('meito');
        $this->assertTrue($item->ignoresStealth());
    }

	public function testShurikenHasSomeMaxDamageWhenTargetted(){
		$shuriken = Item::findByIdentity('shuriken');
		$this->assertGreaterThan(0, $shuriken->getMaxDamage(new Player()));
	}


	public function testShurikenHasSomeIntegerDamage(){
		$shuriken = Item::findByIdentity('shuriken');
		$this->assertGreaterThan(-1, $shuriken->getRandomDamage());
		$this->assertTrue((bool)is_int($shuriken->getRandomDamage()));
	}	

	public function testAmanitaHasSomeTurnChange(){
		$amanita = Item::findByIdentity('amanita');
		$this->assertGreaterThan(0, $amanita->getMaxTurnChange());
	}

	public function testItemPluralNameExists(){
		$caltrop = Item::findByIdentity('caltrops');
		$shuriken = Item::findByIdentity('shuriken');
		$this->assertInstanceOf('NinjaWars\core\data\Item', $caltrop);
		$this->assertInstanceOf('NinjaWars\core\data\Item', $shuriken);
		$this->assertEquals('Shuriken', $shuriken->getName());
		$this->assertEquals('Shuriken', $shuriken->getPluralName());
		$this->assertEquals('Caltrops', $caltrop->getName());
		$this->assertEquals('Caltrops', $caltrop->getPluralName());
	}
}
