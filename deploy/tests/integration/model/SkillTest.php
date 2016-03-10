<?php
use NinjaWars\core\data\SkillDAO;
use NinjaWars\core\data\Skill;

class SkillTest extends PHPUnit_Framework_TestCase {
	public function setUp(){
	}

	public function tearDown(){
		// Delete test users created in body of test functions
		TestAccountCreateAndDestroy::purge_test_accounts();
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
        $skills = new Skill();
        $passfail = $skills->hasSkill('Fire Bolt', 'tchalvak');
        $this->assertTrue($passfail);
    }
}
