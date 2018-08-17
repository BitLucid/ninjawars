<?php
use NinjaWars\core\data\Skill;
use NinjaWars\core\data\Player;

class SkillUnitTest extends NWTest {
    private $skill;

    public function setUp() {
        parent::setUp();
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
        $this->assertEquals(2, $this->skill->getTurnCost('deflect'));
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

    public function testStalkingAffectsStats(){
        $pc = new Player();
        $str = $pc->getStrength();
        $speed = $pc->getSpeed();
        $stamina = $pc->getStamina();
        $pc->addStatus(STALKING);
        $this->assertTrue($pc->hasStatus(STALKING));
        $this->assertGreaterThan($str, $pc->getStrength());
        $this->assertLessThan($speed, $pc->getSpeed());
        $this->assertLessThan($stamina, $pc->getStamina());
    }

    public function testGetIgnoreStealthDefault() {
        $this->assertFalse($this->skill->getIgnoreStealth(''));
    }

    public function testGetIgnoreStealthSpecific() {
        $this->assertTrue($this->skill->getIgnoreStealth('blaze'));
    }

    public function testStealthDecreasesStrengthIncreasesStamina() {
        $pc = new Player();
        $str = $pc->getStrength();
        $stamina = $pc->getStamina();
        $speed = $pc->getSpeed();
        $pc->addStatus(STEALTH);
        $this->assertTrue($pc->hasStatus(STEALTH));
        $this->assertLessThan($str, $pc->getStrength(), 'Stealth failed to affect strength.');
        $this->assertGreaterThan($stamina, $pc->getStamina(), 'Stealth status failed to affect stamina.');
        $this->assertGreaterThan($speed, $pc->getSpeed(), 'Stealth status failed to affect speed.');
    }
}
