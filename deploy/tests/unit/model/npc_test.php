<?php
/**
 * Mob npcs and their combat behavior for simple attacking on the npc page
 *
**/
require_once(realpath(__DIR__.'/../../../').'/resources.php');
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

	public function testBlankNpcHasZeroStrengthPositiveHealth(){
		$npc = new Npc($data=array());
		$this->assertEquals(0, $npc->strength());
		$this->assertGreaterThan(0, $npc->health()); // All npcs should actually get some health!
	}

	public function testForPresenceOfSomeNPCData(){
		$this->assertTrue(array_key_exists('fireflies', NpcFactory::npcsData()), 'Fireflies not present in npcs data array for some reason');
		$this->assertTrue(array_key_exists('firefly', NpcFactory::npcsData()), 'Firefly not present in npcs data array for some reason');
		$this->assertTrue(array_key_exists('spider', NpcFactory::npcsData()));
		$this->assertTrue(array_key_exists('kappa', NpcFactory::npcsData()));
		$this->assertTrue(array_key_exists('tengu', NpcFactory::npcsData()));
		if(defined('DEBUG') && DEBUG){
			$this->assertTrue(array_key_exists('pig', NpcFactory::npcsData()));
			$this->assertTrue(array_key_exists('merchant2', NpcFactory::npcsData()));
			$this->assertTrue(array_key_exists('peasant2', NpcFactory::npcsData()));
		}
	}

	public function testCreateStandardFirefly(){
		assert(defined('DEBUG') && DEBUG);
		$firefly = NpcFactory::create('firefly');
		$this->assertInstanceOf('Npc', $firefly, 'Firefly creation failed');
	}

	public function testCreateStandardFirefliesPlural(){
		assert(defined('DEBUG') && DEBUG);
		$fireflies = NpcFactory::create('fireflies');
		$this->assertInstanceOf('Npc', $fireflies, 'Fireflies creation failed');
	}

	public function testNpcListHasLotsOfNpcs(){
		$min_npcs = defined('DEBUG') && DEBUG? 15 : 8;
		$this->assertGreaterThan($min_npcs, count(NpcFactory::npcs()));
	}

	public function testNpcListSortedByDifficultyGetsEasyNpcLast(){
		$npcs_by_diff = NpcFactory::allSortedByDifficulty();
		$first_npc = reset($npcs_by_diff);
		$last_npc = array_pop($npcs_by_diff);
		$this->assertLessThan(10, $first_npc->difficulty());
		$this->assertEquals('Ryu', $last_npc->identity()); // For now ryu will be the testable top npc.
		$this->assertGreaterThan(300, $last_npc->difficulty());
	}

	// Npcs should have damage, assuming they're combat npcs, which most are
	public function testNpcHasDamage(){
		$npcs = NpcFactory::npcs();
		unset($npcs['Firefly'], $npcs['Fireflies']);
		foreach($npcs as $npc){
			$this->assertTrue($npc  instanceof Npc);
			$this->assertGreaterThan(0, $npc->max_damage());
		}
	}

	// Npcs should always have some health
	public function testNpcsAlwaysHaveHealth(){
		$npcs = NpcFactory::npcs();
		foreach($npcs as $npc){
			$this->assertGreaterThan(0, $npc->health(), 'For npc: ['.$npc->identity().']');
		}
	}

	// Some npcs should cause bounty, generally weaker village peeps
	public function testWeaklingsCauseBounty(){
		if(!(defined('DEBUG') && DEBUG)){
			$this->markTestSkipped(); // No merchant2 in non-debug scenarios for now.
		} else {
			$merchant = new Npc('merchant2');
			$this->assertGreaterThan(0, $merchant->bounty());
			$villager = new Npc('peasant2');
			$this->assertGreaterThan(0, $villager->bounty());
		}
	}

	// Npcs have similar races, e.g. a guard and a villager.
	public function testVariousVillagersHaveSameRace(){
		if(!(defined('DEBUG') && DEBUG)){
			$this->markTestSkipped();
		} else {
			$humans = array('peasant2', /*'theif2', */'guard2', 'merchant2');
			foreach($humans as $human){
				$this->assertEquals('human', (new Npc($human))->race());
			}
		}
	}

<<<<<<< HEAD
	public function testPeasant2AbstractNpcIsSimilarToOriginal(){
		if(!DEBUG){
			$this->markTestSkipped();
		}
		// Peasant damage is 0-10
		// Peasant gold is between 0 and 20.
		// 1 in 20 chance of being disguised ninja.
		// Has added bounty if attacker is below level 21, and greater than 1.
		// added bounty is 1/3rd of attacker's level.
		// If they were a disguised ninja, they should drop the max inventory.
		$peasant = new Npc('peasant2');
		$this->assertLessThan(13, $peasant->max_damage());
		$this->assertGreaterThan(0, $peasant->max_damage());
		$this->assertLessThan(21, $peasant->gold());
		$mock_pc = new Player();
		$mock_pc->vo->level = 10;
		$this->assertEquals(10, $mock_pc->level());
		$this->assertGreaterThan(0, $peasant->dynamicBounty($mock_pc));
	}

	public function testMerchant2AbstractNpcIsSimilarToOriginal(){
		if(!DEBUG){
			$this->markTestSkipped();
		}
		$merchant2 = new Npc('merchant2');
		/*
		Merchant1:
		Damage 15 - 35
		Gold 20 - 70
		?? 70% chance of phosphor powder drop?
		// 20 gold bounty (ish) from killing


		*/
		$this->assertLessThan(37, $merchant2->max_damage());
		$this->assertGreaterThan(15, $merchant2->max_damage());
		$this->assertLessThan(70, $merchant2->gold());
		$this->assertGreaterThan(20, $merchant2->gold());
		$this->assertGreaterThan(0, $merchant2->bounty());
		$this->assertLessThan(25, $merchant2->bounty());
	}

	// Npcs have different difficulties
	function testNpcDifficultiesAreDifferent(){
		$ff = new Npc('fireflies');
		$tengu = new Npc('tengu');
		$this->assertGreaterThan(0, $tengu->difficulty());
		$this->assertGreaterThan($ff->difficulty(), $tengu->difficulty());
	}


	function testDefaultRaceForBasanIsCreature(){
		if(!DEBUG){
			$this->markTestSkipped();
		}
		$npc = new Npc('basan');
		$this->assertEquals('creature', $npc->race());
	}




=======
	function testGuardsThatMatchStrengthTakeEnemyStrength(){
		$pc = new Player();
		$pc->vo->strength = 100;
		$guard = new Npc('guard2');
		$guard_strength = $guard->strength();
		$guard_max_damage = $guard->max_damage();
		$guard_with_enemy = new Npc('guard2');
		$improved_dam = $guard_with_enemy->max_damage($pc);
		$this->assertTrue($guard->has_trait('partial_match_strength'));
		$this->assertGreaterThan(0, $guard_max_damage);
		$this->assertGreaterThan($guard_max_damage, $improved_dam, 'Guard damage should be higher with an enemy that has any strength');
	}

>>>>>>>   Guard: Partial_match_strength trait now in play.
}