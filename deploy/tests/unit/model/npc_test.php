<?php
require_once(ROOT.'core/data/Npc.php');

class Npc_Test extends PHPUnit_Framework_TestCase {


	public function setUp()
	{
	}

	public function tearDown()
	{
	}

	public function testInstantiatingABlankNpc(){
		$npc = new Npc($data=array());
		$this->assertTrue($npc instanceof Npc);
	}

	public function testCreateStandardFireflyAndFireflies(){
		assert(defined('DEBUG') && DEBUG);
		$firefly = NpcFactory::create('firefly');
		$this->assertInstanceOf('Npc', $firefly);
		$fireflies = NpcFactory::create('fireflies');
		$this->assertInstanceOf('Npc', $fireflies);
	}

	// Npcs should have damage, assuming they're combat npcs, which most are
	public function testNpcHasDamage(){
		$npcs = NpcFactory::npcs();
		unset($npcs['firefly'], $npcs['fireflies']);
		foreach($npcs as $npc){
			$this->assertTrue($npc  instanceof Npc);
			$this->assertGreaterThan(0, $npc->max_damage());
		}
	}

	// Npcs should always have some health
	public function testNpcsAlwaysHaveHealth(){
		$npcs = NpcFactory::npcs();
		foreach($npcs as $npc){
			$this->assertGreaterThan(0, $npc->health());
		}
	}

	// Some npcs should cause bounty, generally weaker village peeps
	public function testWeaklingsCauseBounty(){
		$villager = new Npc('villager2');
		$this->assertGreaterThan(0, $villager->bounty());
		$merchant = new Npc('merchant2');
		$this->assertGreaterThan(0, $merchant->bounty());
	}

	// Npcs have similar races, e.g. a guard and a villager.
	public function testVariousVillagersHaveSameRace(){
		$humans = array('villager2', 'theif2', 'guard2', 'merchant2');
		foreach($humans as $human){
			$this->assertEquals('human', (new Npc($human))->race());
		}
	}

}