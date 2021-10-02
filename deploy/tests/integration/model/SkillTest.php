<?php
use NinjaWars\core\data\SkillDAO;
use NinjaWars\core\data\Skill;
use NinjaWars\tests\MockPlayer;

class SkillTest extends NWTest {

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
        $pc->setLevel(99);
        $skills = new Skill();
        $passfail = $skills->hasSkill('Fire Bolt', $pc);
        $this->assertTrue($passfail, 'Admin user does not have access to firebolt skill as expected');
    }

    public function testAdminPlayerGetsUnstealthSkill(){
        $pc = new MockPlayer();
        $pc->setAdmin(true);
        $skills = new Skill();
        $passfail = $skills->hasSkill('Unstealth', $pc);
        $this->assertTrue($passfail, 'Admin user does not have access to skill as expected');
    }

    public function testAdminPlayerGetsDuel(){
        $pc = new MockPlayer();
        $pc->setAdmin(true);
        $skills = new Skill();
        $passfail = $skills->hasSkill('Duel', $pc);
        $this->assertTrue($passfail, 'Admin user does not have access to skill as expected');
    }

    public function testAdminPlayerGetsStealthSkill(){
        $pc = new MockPlayer();
        $pc->setAdmin(true);
        $pc->setLevel(99);
        $skills = new Skill();
        $passfail = $skills->hasSkill('Unstealth', $pc);
        $this->assertTrue($passfail, 'Admin user does not have access to skill as expected');
    }

    public function testAdminPlayerGetsBlazeSkill(){
        $pc = new MockPlayer();
        $pc->setAdmin(true);
        $pc->setLevel(99);
        $skills = new Skill();
        $passfail = $skills->hasSkill('Blaze', $pc);
        $this->assertTrue($passfail, 'Admin user does not have access to skill as expected');
    }
}
