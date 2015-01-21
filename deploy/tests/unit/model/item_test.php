<?php
require_once(realpath(__DIR__.'/../../../').'/resources.php');
require_once(ROOT.'core/base.inc.php');
require_once(ROOT.'core/data/Item.php');

class Item_Test extends PHPUnit_Framework_TestCase {


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

	public function testRetrievingAShuriken(){
		$shuriken = new Item('shuriken');
		$this->assertTrue($shuriken instanceof Item);
		$this->assertEquals('shuriken', $shuriken->identity());
	}

	public function testTessenAndKunaiHaveSomeMaxDamage(){
		$tessen = new Item('tessen');
		$this->assertGreaterThan(0, $tessen->getMaxDamage());
		$kunai = new Item('kunai');
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
		$kunai = new Item('kunai');
		$sum = $this->itemRandomDamageSum($kunai);
		$this->assertGreaterThan(0, $sum);
	}

	public function weaponsHaveSomeRandomDamage(){
		// Special cases:  Shuriken...
		$this->assertGreaterThan(0, $this->itemRandomDamageSum(new Item('kunai')));
		$this->assertGreaterThan(0, $this->itemRandomDamageSum(new Item('ono')));
		$this->assertGreaterThan(0, $this->itemRandomDamageSum(new Item('bo')));
		$this->assertGreaterThan(0, $this->itemRandomDamageSum(new Item('kama')));
		$this->assertGreaterThan(0, $this->itemRandomDamageSum(new Item('tetsubo')));
	}

	public function testShurikenHasSomeMaxDamage(){
		$shuriken = new Item('shuriken');
		$this->assertGreaterThan(0, $shuriken->getMaxDamage(new Player()));
	}


	public function testShurikenHasSomeIntegerDamage(){
		$shuriken = new Item('shuriken');
		$this->assertGreaterThan(-1, $shuriken->getRandomDamage());
		$this->assertTrue((bool)is_int($shuriken->getRandomDamage()));
	}	

	public function testAmanitaHasSomeTurnChange(){
		$amanita = new Item('amanita');
		$this->assertGreaterThan(0, $amanita->getMaxTurnChange());
	}
}