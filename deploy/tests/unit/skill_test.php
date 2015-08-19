<?php
// Note that the file has to have a file ending of ...test.php to be run by phpunit


class CloneKill{

    public static function kill($clone1, $clone2){
        // Input is transformed into 
        $id1 = $id2 = null;
        if($clone1 instanceof Player){
            $id1 = $clone1->id();
        } elseif ($clone1 == positive_int($clone1)){
            $id1 = $clone1;
        } elseif (is_string($clone1)){
            $id1 = get_char_id($clone1);
        }
        if($clone2 instanceof Player){
            $id2 = $clone2->id();
        } elseif ($clone2 == positive_int($clone2)){
            $id2 = $clone2;
        } elseif (is_string($clone2)){
            $id2 = get_char_id($clone2);
        }
        // Reject same character
        if($id1 == $id2){
            return false;
        }
        // Reject inactive characters
        // Reject inoperative characters
        // If characters have the same joint account, and have been logged in recently...

        

    }
}

class TestSkill extends PHPUnit_Framework_TestCase {


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

    public function testCloneKillDoesNotAllowYouToCloneKillYourself(){
        $char_id = TestAccountCreateAndDestroy::char_id();
        $this->assertFalse(CloneKill::kill($char_id, $char_id));
    }

    public function testYouCantCloneKillXAndX(){
        $this->markTestIncomplete();
    }

    public function testYouCantCloneKillEmpties(){
        $this->markTestIncomplete();
    }

    public function testYouCantCloneKillWithAnyNonActiveChar(){
        $this->markTestIncomplete();
    }

    public function testYouCantCloneKillWithAnyNonConfirmedAccounts(){
    }

    public function testYouCantCloneKillWithAnyNonOperationalAccounts(){
    }

    public function testCloneKillOnCharsOfSameAccountSameIpWorks(){
        $this->markTestIncomplete();
    }

    public function testCloneKillOnCharsOfSameAccountDifferentIpWorks(){
        $this->markTestIncomplete();
    }


}

