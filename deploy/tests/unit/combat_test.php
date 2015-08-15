<?php
// Note that the file has to have a file ending of ...test.php to be run by phpunit
require_once(ROOT.'core/control/AttackLegal.class.php');


class TestAttackLegal extends PHPUnit_Framework_TestCase {


	/**
	 * group char
	**/
	function setUp(){
	}
	
	/**
	 * group char
	**/
	function tearDown(){
		// Delete test user.
		TestAccountCreateAndDestroy::purge_test_accounts();
    }

	// Test that you can't self-attack.
    public function testAttackLegalCantAttackSelf(){
        $char_id = TestAccountCreateAndDestroy::create_testing_account();
    	$legal = new AttackLegal($char_id, $char_id, ['required_turns'=>1, 'ignores_stealth'=>true]);
    	$this->assertFalse($legal->check($update_timer=false));
    }

    public function testAttackLegalCantAttackSelfEvenIfUsingSelfIdVsSelfUsername(){
        $char_id = TestAccountCreateAndDestroy::create_testing_account();
        $info = char_info($char_id);
        $this->assertTrue((bool)$info['uname'], 'Character uname not found to check attacklegal with');
        $legal = new AttackLegal($char_id, $info['uname'], ['required_turns'=>1, 'ignores_stealth'=>true]);
        $this->assertFalse($legal->check($update_timer=false));
    }

    // Test that you can attack as two separate characters.
    public function testCanAttackAsTwoSeparateCharacters(){
        $confirm = true;
    	$char_id = TestAccountCreateAndDestroy::create_testing_account($confirm);
        $char_2_id = TestAccountCreateAndDestroy::create_alternate_testing_account($confirm);
        $legal = new AttackLegal($char_id, $char_2_id, ['required_turns'=>1, 'ignores_stealth'=>true]);
        $checked = $legal->check($update_timer=false);
        $this->assertEquals(null, $legal->getError(), 'There was an attack error message when there shouldn\'t be one.');
        $this->assertTrue($checked);
    }

    public function testCanAttackAsOneCharByIdAndAnotherByName(){
        $confirm = true;
        $char_id = TestAccountCreateAndDestroy::create_testing_account($confirm);
        $char_2_id = TestAccountCreateAndDestroy::create_alternate_testing_account($confirm);
        $char2 = new Player($char_2_id);
        $legal = new AttackLegal($char_id, $char2->name(), ['required_turns'=>1, 'ignores_stealth'=>true]);
        $checked = $legal->check($update_timer=false);
        $this->assertEquals(null, $legal->getError(), 'There was an attack error message when there shouldn\'t be one.');
        $this->assertTrue($checked);
    }


    // Test that you can't attack if an excessive amount of turns is required  
    public function testCantAttackIfExcessiveAmountOfTurnsIsRequired(){
        $confirm = true;
        $char_id = TestAccountCreateAndDestroy::create_testing_account($confirm);
        $char_2_id = TestAccountCreateAndDestroy::create_alternate_testing_account($confirm);
        $char = new Player($char_2_id);
        $legal = new AttackLegal($char_id, $char->name(), ['required_turns'=>4000000000, 'ignores_stealth'=>true]);
        $this->assertFalse($legal->check($update_timer=false));
    }


}

