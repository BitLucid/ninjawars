<?php
use NinjaWars\core\control\Combat;
use NinjaWars\core\data\Player;
use NinjaWars\core\data\Character;
use NinjaWars\core\data\NpcFactory;
use NinjaWars\tests\MockPlayer;
use \TestAccountCreateAndDestroy;

class CombatTest extends \PHPUnit_Framework_TestCase {

    public function testKillPointCalculation() {
        $char = new MockPlayer();

        $this->assertInternalType(
            'int',
            Combat::killPointsFromDueling(
                $char,
                $char
            )
        );
    }

    public function testBountyExchangeWithBountyLessEqualPcs(){
        //$pc = TestAccountCreateAndDestroy::char();
        $pc = new MockPlayer();
        $pc->difficulty = 1;
        $pc->level = 1;
        $pc->bounty = 0;

        $def = new MockPlayer();
        $def->difficulty = 1;
        $def->level = 1;
        $def->bounty = 0;

        $bounty_mod = 0;

        $bounty_mess = Combat::runBountyExchange($pc, $def, $bounty_mod);
        // Equal pcs, no bounty message
        $this->assertEquals(null, $bounty_mess);
    }

    public function testBountyExchangeWithSomeBountyOnDefender(){
        //$pc = TestAccountCreateAndDestroy::char();
        $pc = new MockPlayer();
        $pc->difficulty = 1;
        $pc->level = 1;
        $pc->bounty = 0;

        $def = new MockPlayer();
        $def->difficulty = 1;
        $def->level = 100;
        $def->bounty = 1000;

        $bounty_mod = 0;

        $bounty_mess = Combat::runBountyExchange($pc, $def, $bounty_mod);
        // Equal pcs, no bounty message
        $this->assertNotEquals(null, $bounty_mess);
    }

    public function testBountyExchangeWithInequalPcs(){
        //$pc = TestAccountCreateAndDestroy::char();
        $pc = new MockPlayer();
        $pc->difficulty = 100;
        $pc->level = 100;
        $pc->bounty = 0;

        $def = new MockPlayer();
        $def->difficulty = 1;
        $def->level = 1;
        $def->bounty = 0;
        $bounty_mod = 0;

        $bounty_mess = Combat::runBountyExchange($pc, $def, $bounty_mod);
        // With a high difficulty pc, some bounty should be put on.
        $this->assertNotEquals(null, $bounty_mess);
        //$this->assertGreaterThan(0, $pc->bounty);
    }

    public function testBountyExchangeWithPowerfulPCWeakNpc(){
        //$pc = TestAccountCreateAndDestroy::char();
        $pc = new MockPlayer();
        $pc->difficulty = 30;
        $pc->level = 30;
        $npc = new MockNpc();
        $npc->bountyMod = 20;
        $bounty_mod = 100;

        $bounty_mess = Combat::runBountyExchange($pc, $npc, $bounty_mod);
        // With a high powered pc, some bounty should be put on by attacking a low powered npc.
        $this->assertNotEquals(null, $bounty_mess);
    }

    public function testBountyDoesntGrowOutOfBounds(){
        $pc = new MockPlayer();
        $pc->difficulty = 2345;
        $pc->bounty = 5000;
        $bounty_mod = 4000;

        $def = new MockPlayer();
        $def->bounty = 0;

        $bounty_mess = Combat::runBountyExchange($pc, $def, $bounty_mod);
        $this->assertEquals(null, $bounty_mess);
    }


    public function testBountyAtLeastBountyModFromNpcs(){
        //$pc = TestAccountCreateAndDestroy::char();
        $pc = new MockPlayer();
        $pc->difficulty = 0;
        $pc->level = 1;
        $npc = new MockNpc();
        $npc->bountyMod = 20;
        $bounty_mod = 0;

        $bounty_mess = Combat::runBountyExchange($pc, $npc, $bounty_mod);
        // With a high powered pc, some bounty should be put on by attacking a low powered npc.
        $this->assertNotEquals(null, $bounty_mess);
    }
}
