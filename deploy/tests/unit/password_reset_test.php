<?php
// Note that the file has to have a file ending of ...test.php to be run by phpunit
require_once(CORE.'data/PasswordResetRequest.php');
require_once(ROOT.'tests/TestAccountCreateAndDestroy.php');


class TestPasswordReset extends PHPUnit_Framework_TestCase {

	function setUp(){
        $this->account_id = TestAccountCreateAndDestroy::account_id();
        assert($this->account_id && ($this->account_id>0));
        assert(query_item('select account_id from accounts where account_id = :id limit 1', [':id'=>$this->account_id]));
        $this->account_info = account_info($this->account_id);
        assert(!empty($this->account_id));
        $this->ninja = new Player(TestAccountCreateAndDestroy::char_id());
	}
	
	function tearDown(){
        TestAccountCreateAndDestroy::purge_test_accounts();
        query("delete from password_reset_requests where nonce = '777777'");
    }

    public function testFindAccountForMakingResetAccountHasToExist(){
        $account = PasswordResetRequest::findAccount($this->account_info['active_email'], null);
        $this->assertGreaterThan(0, $account->getId());
        $this->assertTrue(is_numeric($account->getId()));
        //$this->assertTrue(PasswordResetRequest::findAccount(null, $this->account_info['active_email']));
        $final_account = PasswordResetRequest::findAccount(null, $this->ninja->name());
        $this->assertGreaterThan(0, $final_account->getId());
    }

    // Make sure a user can make a request
    public function testCreatePasswordResetEntry(){
        //var_dump($this->account_id);
        //var_dump(query_item('select account_id from accounts where account_id = :id', [':id'=>$this->account_id]));
        $this->assertTrue(PasswordResetRequest::request(query_item('select account_id from accounts limit 1')));
    }

    // Retrieve the password request with the appropriate nonce data
    public function testRetrieveCreatedPasswordReset(){
        $this->assertTrue(PasswordResetRequest::request(query_item('select account_id from accounts limit 1'), $nonce='777777')); // Create
        $req = PasswordResetRequest::match($nonce); // Match
        $this->assertEquals($nonce, $req['nonce']);
    }

    // Reject resets that don't contain a new password
    public function testRejectionOfResetsThatDontHaveANewPassword(){
        $account_id = TestAccountCreateAndDestroy::account_id();
        $this->assertFalse(PasswordResetRequest::reset($account_id, null));
    }

    // Reject resets that don't have a valid account_id
    public function testRejectionOfResetsThatDontHaveAValidAccountId(){
        $this->assertFalse(PasswordResetRequest::reset(1234567890, 'some_valid_password'));
    }

    public function testResetOfpasswordWhenCorrectDataGiven(){
        $account_id = TestAccountCreateAndDestroy::account_id();
        $this->assertTrue(PasswordResetRequest::reset($account_id, 'some_password%FW@G'));
    }

    public function testResetOfpasswordWhenCorrectDataGivenWithAlternatePasswordUsage(){
        $account_id = TestAccountCreateAndDestroy::account_id();
        $this->assertTrue(PasswordResetRequest::reset($account_id, 'SDGAERHQEW$$%Y$%'));
    }


}

