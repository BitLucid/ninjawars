<?php
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

	public function testShurikenHasSomeMaxDamage(){
		$this->markTestIncomplete();
		$shuriken = new Item('shuriken');
		$this->assertGreaterThan(0, $shuriken->getMaxDamage());
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