<?php

use NinjaWars\core\data\NpcFactory;
use NinjaWars\core\data\Npc;
use NinjaWars\core\data\Player;

class NpcUnitTest extends NWTest
{
    public function testInstantiatingABlankNpc()
    {
        $npc = new Npc(array());
        $this->assertTrue($npc instanceof Npc);
    }

    public function testBlankNpcHasZeroStrengthPositiveHealth()
    {
        $npc = new Npc(array());
        $this->assertEquals(0, $npc->getStrength());
        $this->assertGreaterThan(0, $npc->getHealth()); // All npcs should actually get some health!
    }

    public function testForPresenceOfSomeNPCData()
    {
        $this->assertTrue(array_key_exists('fireflies', NpcFactory::npcsData()), 'Fireflies not present in npcs data array for some reason');
        $this->assertTrue(array_key_exists('firefly', NpcFactory::npcsData()), 'Firefly not present in npcs data array for some reason');
        $this->assertTrue(array_key_exists('spider', NpcFactory::npcsData()));
        $this->assertTrue(array_key_exists('kappa', NpcFactory::npcsData()));
        $this->assertTrue(array_key_exists('tengu', NpcFactory::npcsData()));
    }


    public function testCreateStandardFirefly()
    {
        $firefly = NpcFactory::create('firefly');
        $this->assertInstanceOf('NinjaWars\core\data\Npc', $firefly, 'Firefly creation failed');
    }

    public function testCreateStandardFirefliesPlural()
    {
        $fireflies = NpcFactory::create('fireflies');
        $this->assertInstanceOf('NinjaWars\core\data\Npc', $fireflies, 'Fireflies creation failed');
    }

    public function testForExperimentalNpcs()
    {
        if (!DEBUG) {
            $this->markTestSkipped();
        }
        $this->assertTrue(array_key_exists('pig', NpcFactory::npcsData()));
        $this->assertTrue(array_key_exists('merchant2', NpcFactory::npcsData()));
        $this->assertTrue(array_key_exists('peasant2', NpcFactory::npcsData()));
    }

    public function testBasanNpcWithBaseCreatureRaceWorks()
    {
        if (!DEBUG) {
            $this->markTestSkipped();
        }
        $basan = NpcFactory::create('basan'); // Weird cockatrice bird thing, I made it race default creature
        $this->assertInstanceOf('NinjaWars\core\data\Npc', $basan, 'Basan base race creature creation failed');
        $this->assertTrue($basan->race() === 'creature');
    }

    public function testNpcListHasLotsOfNpcs()
    {
        $min_npcs = defined('DEBUG') && DEBUG ? 15 : 8;
        $this->assertGreaterThan($min_npcs, count(NpcFactory::npcs()));
    }

    public function testNpcListSortedByDifficultyGetsEasyNpcLast()
    {
        $npcs_by_diff = NpcFactory::allSortedByDifficulty();
        $first_npc = reset($npcs_by_diff);
        $last_npc = array_pop($npcs_by_diff);
        $this->assertLessThan(10, $first_npc->difficulty());
        $this->assertEquals('Ryu', $last_npc->identity()); // For now ryu will be the testable top npc.
        $this->assertGreaterThan(300, $last_npc->difficulty());
    }

    /**
     * Npcs should have damage, assuming they're combat npcs, which most are
     */
    public function testNpcHasDamage()
    {
        $npcs = NpcFactory::npcs();
        unset($npcs['Firefly'], $npcs['Fireflies']);

        foreach ($npcs as $npc) {
            $this->assertTrue($npc instanceof Npc);
            $this->assertGreaterThan(0, $npc->maxDamage());
        }
    }

    /**
     * Npcs should always have some health
     */
    public function testNpcsAlwaysHaveHealth()
    {
        $npcs = NpcFactory::npcs();

        foreach ($npcs as $npc) {
            $this->assertGreaterThan(0, $npc->getHealth(), 'For npc: ['.$npc->identity().']');
        }
    }

    /**
     * Some npcs should cause bounty, generally weaker village peeps
     */
    public function testWeaklingsCauseBounty()
    {
        if (!(defined('DEBUG') && DEBUG)) {
            $this->markTestSkipped(); // No merchant2 in non-debug scenarios for now.
        } else {
            $merchant = new Npc('merchant2');
            $this->assertGreaterThan(0, $merchant->bountyMod());
            $villager = new Npc('peasant2');
            $this->assertGreaterThan(0, $villager->bountyMod());
        }
    }

    /**
     * Npcs have similar races, e.g. a guard and a villager.
     */
    public function testVariousVillagersHaveSameRace()
    {
        if (!(defined('DEBUG') && DEBUG)) {
            $this->markTestSkipped();
        } else {
            $humans = array('peasant2', /*'thief2', */ 'guard2', 'merchant2');
            foreach ($humans as $human) {
                $this->assertEquals('human', (new Npc($human))->race());
            }
        }
    }

    /**
     * Peasant damage is 0-10
     * Peasant gold is between 0 and 20.
     * 1 in 20 chance of being disguised ninja.
     * Has added bounty if attacker is below level 21, and greater than 1.
     * added bounty is 1/3rd of attacker's level.
     * If they were a disguised ninja, they should drop the max inventory.
     */
    public function testPeasant2AbstractNpcIsSimilarToOriginal()
    {
        if (!DEBUG) {
            $this->markTestSkipped();
        }

        $peasant = new Npc('peasant2');
        $this->assertLessThan(13, $peasant->maxDamage());
        $this->assertGreaterThan(0, $peasant->maxDamage());
        $this->assertLessThan(21, $peasant->gold());
        $mock_pc = new Player();
        $mock_pc->level = 10;
        $this->assertEquals(10, $mock_pc->level);
        $this->assertGreaterThan(0, $peasant->bountyMod());
    }

    /**
     * Merchant1:
     * Damage 15 - 35
     * Gold 20 - 70
     * ?? 70% chance of phosphor powder drop?
     * 20 gold bounty (ish) from killing
     */
    public function testMerchant2AbstractNpcIsSimilarToOriginal()
    {
        if (!DEBUG) {
            $this->markTestSkipped();
        }

        $merchant2 = new Npc('merchant2');
        $this->assertLessThan(37, $merchant2->maxDamage());
        $this->assertGreaterThan(15, $merchant2->maxDamage());
        $this->assertLessThan(70, $merchant2->gold());
        $this->assertGreaterThan(20, $merchant2->gold());
        $this->assertGreaterThan(0, $merchant2->bountyMod());
        $this->assertLessThan(25, $merchant2->bountyMod());
    }

    /**
     * Guard1:
     * Dam: 1 to attacker_str + 40
     * Gold: 1 to attacker_str + 40
     * Bounty: 10 + 0-10
     * 1 in 9 chance of ginseng root
     * Guard2: Strength is about 30, which is multiplied by 2, + 1 point during damage calc.
     * Gold doesn't get boosted by strength
     */
    public function testGuard2AbstractNpcIsSimilarToGuard1()
    {
        if (!DEBUG) {
            $this->markTestSkipped();
        }

        $guard2 = new Npc('guard2');
        $mock_pc = new Player();
        $mock_pc->setStrength(30);
        $dam = $guard2->maxDamage($mock_pc); // partial_match_strength should add about 1/3rd of the enemies' strength as dam.
        $this->assertGreaterThan(40, $dam);
        $this->assertLessThan(80, $dam); // Dam is strength * 2 + 1
        $this->assertLessThan(61, $guard2->gold());
        $this->assertGreaterThan(40, $guard2->gold());
        $this->assertGreaterThan(9, $guard2->bountyMod());
    }


    public function testThief2DoesStuff()
    {
        if (!DEBUG) {
            $this->markTestSkipped();
        }
        /**
         * Thief should have a max damage of 35
         * it should have "hitpoints" of 30
         * it should have zero reward gold
         * it should sometimes be able to steal the equivalent of it's reward gold
         * it should always give a shuriken as long as it was "killed"
         * also adds to the thief counter based on being in a gang, and thus has a chance
         * for the gang to enact retribution (though this won't be tested in unit behavior)
         */
        $thief2 = new Npc('thief2');
        $mock_pc = new Player();
        $mock_pc->setStrength(30);
        $max_dam = $thief2->maxDamage($mock_pc);

        $this->assertEquals(35, $max_dam);
        $this->assertLessThan(50, $thief2->damage());
        $this->assertGreaterThan(29, $thief2->getHealth());
        $this->assertLessThan(80, $thief2->getHealth());
        $this->assertLessThan(61, $thief2->gold());
        $this->assertGreaterThan(1, $thief2->gold());
        $this->assertLessThan(1, $thief2->bountyMod());
        $this->assertTrue($thief2->hasTrait('steals'));
        $this->assertTrue($thief2->hasTrait('escaper'));
        $this->assertTrue($thief2->hasTrait('gang'));
        $this->assertGreaterThan(45, $thief2->difficulty());
        $this->assertLessThan(60, $thief2->difficulty());
    }

    public function testAnNpcHasADifficulty()
    {
        if (!DEBUG) {
            $this->markTestSkipped();
        }
        $peasant = new Npc('peasant2');
        $this->assertGreaterThan(0, $peasant->difficulty());
    }

    public function testNpcDifficultiesAreDifferent()
    {
        $firefly = new Npc('fireflies');
        $tengu = new Npc('tengu');
        $this->assertGreaterThan(0, $tengu->difficulty());
        $this->assertGreaterThan($firefly->difficulty(), $tengu->difficulty());
    }

    public function testDefaultRaceForBasanIsCreature()
    {
        if (!DEBUG) {
            $this->markTestSkipped();
        }

        $npc = new Npc('basan');
        $this->assertEquals('creature', $npc->race());
    }

    public function testGuardsThatMatchStrengthTakeEnemyStrength()
    {
        if (!DEBUG) {
            $this->markTestSkipped();
        }

        $player = new Player();
        $player->strength = 100;
        $guard = new Npc('guard2');
        $guard_max_damage = $guard->maxDamage();
        $guard_with_enemy = new Npc('guard2');
        $improved_dam = $guard_with_enemy->maxDamage($player);
        $this->assertTrue($guard->hasTrait('partial_match_strength'));
        $this->assertGreaterThan(0, $guard_max_damage);
        $this->assertGreaterThan($guard_max_damage, $improved_dam, 'Guard damage should be higher with an enemy that has any strength');
    }

    public function testDifficultiesOfDifferentMobsIncreases()
    {
        if (!DEBUG) {
            $this->markTestSkipped();
        }

        $this->assertGreaterThan(0, (new Npc('guard2'))->difficulty(), 'zero vs guard2 difficulty mismatch');
        $this->assertGreaterThan((new Npc('peasant2'))->difficulty(), (new Npc('guard2'))->difficulty(), 'peasant vs guard2 difficulty mismatch');
        $this->assertGreaterThan((new Npc('firefly'))->difficulty(), (new Npc('spider'))->difficulty(), 'firefly vs spider difficulty mismatch');
        $this->assertGreaterThan((new Npc('firefly'))->difficulty(), (new Npc('guard2'))->difficulty(), 'firefly vs guard2 difficulty mismatch');
        $this->assertGreaterThan((new Npc('firefly'))->difficulty(), (new Npc('peasant2'))->difficulty(), 'firefly vs peasant2 difficulty mismatch');
        $this->assertGreaterThan((new Npc('pig'))->difficulty(), (new Npc('ox'))->difficulty(), 'pig vs ox difficulty mismatch');
        $this->assertGreaterThan((new Npc('firefly'))->difficulty(), (new Npc('tiger'))->difficulty(), 'firefly vs tiger difficulty mismatch');
        //$this->assertGreaterThan((new Npc('tiger'))->difficulty(), (new Npc('oni'))->difficulty(), 'tiger vs oni difficulty mismatch');
        $this->assertGreaterThan((new Npc('oni'))->difficulty(), (new Npc('ryu'))->difficulty(), 'oni vs ryu difficulty mismatch');
        $this->assertGreaterThan((new Npc('tiger'))->difficulty(), (new Npc('ryu'))->difficulty(), 'tiger vs ryu difficulty mismatch');
    }

    public function testNpcs()
    {
        $npcs = NpcFactory::npcs();
        $this->assertIsArray($npcs);
        $this->assertNotEmpty($npcs);
    }

    public function testAliasAll_for_Npcs()
    {
        $this->assertEqualsCanonicalizing(NpcFactory::all(), NpcFactory::npcs());
    }

    public function testAllNonTrivialNpcs()
    {
        $npcs = NpcFactory::allNonTrivialNpcs();
        $zeroDmgNpcs = [];
        foreach ($npcs as $npc) {
            if ($npc->difficulty() <= 0) {
                $zeroDmgNpcs[] = $npc;
            }
        }
        $this->assertEmpty($zeroDmgNpcs);
    }

    public function testAllTrivialNpcs()
    {
        $npcs = NpcFactory::allTrivialNpcs();
        $damagingNpcs = [];
        foreach ($npcs as $npc) {
            if ($npc->difficulty() > 0) {
                $damagingNpcs[] = $npc;
            }
        }
        $this->assertEmpty($damagingNpcs);
    }

    public function testFleshOutFailure()
    {
        $this->expectException(NinjaWars\core\InvalidNpcException::class);
        NpcFactory::fleshOut('NotARealNPCByAnyMeans', null);
    }
}
