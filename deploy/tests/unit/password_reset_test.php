<?php
// Note that the file has to have a file ending of ...test.php to be run by phpunit
require_once(CORE.'data/PasswordResetRequest.php');
require_once(ROOT.'tests/TestAccountCreateAndDestroy.php');


class TestPasswordReset extends PHPUnit_Framework_TestCase {

	function setUp(){
        $this->account_id = TestAccountCreateAndDestroy::account_id();
        assert($this->account_id>0);
        $this->account_info = account_info($this->account_id);
        $this->ninja_info = new Player(TestAccountCreateAndDestroy::char_id());
	}
	
	function tearDown(){
        TestAccountCreateAndDestroy::purge_test_accounts();
        query("delete from password_reset_requests where nonce = '777777'");
    }

    public function testFindAccountForMakingResetAccountHasToExist(){
        $this->assertGreaterThan(0, PasswordResetRequest::findAccount($this->account_info['active_email'], null));
        //$this->assertTrue(PasswordResetRequest::findAccount(null, $this->account_info['active_email']));
        $this->assertGreaterThan(0, PasswordResetRequest::findAccount(null, $this->ninja_info->name()));
    }

    // Make sure a user can make a request
    public function testCreatePasswordResetEntry(){
        $this->assertTrue(PasswordResetRequest::request(1));
    }

    // Retrieve the password request with the appropriate nonce data
    public function testRetrieveCreatedPasswordReset(){
        $this->assertTrue(PasswordResetRequest::request(1, $nonce='777777'));
        $req = PasswordResetRequest::match($nonce);
        $this->assertEquals($nonce, $req['nonce']);
    }

    // Reject resets that don't contain a new password
    public function testRejectionOfResetsThatDontHaveANewPassword(){
        $account_id = TestAccountCreateAndDestroy::account_id();
        $this->assertFalse(PasswordResetRequest::reset($account_id, null));
    }

    // Reset their password with valid parameters
    public function testResetOfpasswordWhenCorrectDataGiven(){
        $account_id = TestAccountCreateAndDestroy::account_id();
        //var_dump($account_id, account_info($account_id), query_item('select * from accounts where account_id = :id', [':id'=>$this->account_id]));
        //$this->markTestIncomplete('Resetting not yet working quite right!');
        $this->assertTrue(PasswordResetRequest::reset($account_id, 'some_password%FW@G'));
    }


}

