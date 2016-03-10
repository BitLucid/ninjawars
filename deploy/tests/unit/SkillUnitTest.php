<?php
use NinjaWars\core\data\Skill;

class SkillUnitTest extends PHPUnit_Framework_TestCase {
    private $skill;

    public function __construct() {
        $this->skill = new Skill();
    }

    public function testGetSkillList() {
        $skillList = $this->skill->getSkillList();
        $this->assertInternalType('array', $skillList);
        $this->assertNotEmpty($skillList);
    }

    public function testGetTurnCostDefault() {
        $this->assertEquals(1, $this->skill->getTurnCost(''));
    }

    public function testGetTurnCostSpecific() {
        $this->assertEquals(3, $this->skill->getTurnCost('deflect'));
    }

    public function testGetSelfUseDefault() {
        $this->assertFalse($this->skill->getSelfUse(''));
    }

    public function testGetSelfUseSpecific() {
        $this->assertTrue($this->skill->getSelfUse('kampo'));
    }

    public function testGetUsableOnTargetDefault() {
        $this->assertTrue($this->skill->getUsableOnTarget(''));
    }

    public function testGetUsableOnTargetSpecific() {
        $this->assertFalse($this->skill->getUsableOnTarget('stealth'));
    }

    public function testGetIgnoreStealthDefault() {
        $this->assertFalse($this->skill->getIgnoreStealth(''));
    }

    public function testGetIgnoreStealthSpecific() {
        $this->assertTrue($this->skill->getIgnoreStealth('blaze'));
    }
}
