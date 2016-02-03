<?php
require_once(ROOT.'core/control/Skill.php');
require_once(ROOT.'core/data/SkillDAO.php');


use \Skill as Skill;


class TestSkillObj extends PHPUnit_Framework_TestCase {

	public function setUp(){
	}
	
	public function tearDown(){
		// Delete test users created in body of test functions
		TestAccountCreateAndDestroy::purge_test_accounts();
    }


    public function testAdminPlayerGetsFireBoltSkill(){
        $skills = new Skill();
        $passfail = $skills->hasSkill('Fire Bolt', 'tchalvak');
        $this->assertTrue($passfail);
        debug($skills->
    }

}

