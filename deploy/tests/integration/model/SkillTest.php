<?php
use NinjaWars\core\data\SkillDAO;
use NinjaWars\core\data\Skill;
use NinjaWars\tests\MockPlayer;

class SkillTest extends NWTest {
	public function setUp(){
        parent::setUp();
	}

	public function tearDown(){
        parent::tearDown();
    }

    public function testAllSkillsCanBePulled(){
        $skill_list = new SkillDAO();
        $this->assertNotEmpty($skill_list->all());
    }

    public function testPullOfSkillsByType(){
        $skill_list = new SkillDAO();
        $this->assertNotEmpty($skill_list->all('targeted'));
        $this->assertNotEmpty($skill_list->all('combat'));
    }

    public function testAdminPlayerGetsFireBoltSkill(){
        $pc = new MockPlayer();
        $pc->setAdmin(true);
        $skills = new Skill();
        $passfail = $skills->hasSkill('Fire Bolt', $pc);
        $this->assertTrue($passfail);
    }
}
