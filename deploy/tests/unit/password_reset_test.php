<?php
// Note that the file has to have a file ending of ...test.php to be run by phpunit
require_once(CORE.'data/PasswordResetRequest.php');
require_once(ROOT.'tests/TestAccountCreateAndDestroy.php');
require_once(CORE.'data/Account.php');


use app\data\PasswordResetRequest;
use \Account as Account;


class TestPasswordReset extends PHPUnit_Framework_TestCase {

	function setUp(){
        $this->account_id = TestAccountCreateAndDestroy::account_id();
        //assert($this->account_id && ($this->account_id>0));
        // Just hack direct queries in the test setup and teardown.
        //assert(0<query_item('select account_id from accounts where account_id = :id limit 1', [':id'=>$this->account_id]));
        $this->account = new Account($this->account_id);
        $this->nonce = null;
	}
	
	function tearDown(){
        // Don't use naked queries outside of a model layer elsewhere.
        query("delete from password_reset_requests where nonce = '777777' or nonce = '77778877' or nonce = '7777777' or nonce = :nonce or _account_id = :id", 
                [':nonce'=>(isset($this->nonce)? $this->nonce : null), ':id'=>$this->account_id]);
        TestAccountCreateAndDestroy::purge_test_accounts();
        query("delete from password_reset_requests where nonce = '777777' or nonce = '77778877' or nonce = '7777777' or nonce = :nonce or _account_id = :id", 
                [':nonce'=>(isset($this->nonce)? $this->nonce : null), ':id'=>$this->account_id]);
    }

    /**
     * @group first
    **/
    public function testModelCreateARequestWithTheRawModelFunctionality(){
        $req = PasswordResetRequest::create(['_account_id'=>$this->account_id, 'nonce'=>'777666888']);
        $this->assertInstanceOf('app\data\PasswordResetRequest', $req);
        $this->assertGreaterThan(0, (int) $req->nonce);
    }


    // Make sure a user can make a request
    /**
     * @group early
     */
    public function testCreatePasswordResetEntry(){
        $request = PasswordResetRequest::generate($this->account);
        $this->assertTrue((bool)$request->nonce);
    }

    // Retrieve the password request with the appropriate nonce data
    /**
     * @group early
     */
    public function testRetrieveCreatedPasswordReset(){
        $account_id = query_item('select account_id from accounts limit 1');
        $this->assertGreaterThan(0, $account_id);
        $this->nonce='777777';
        $req = PasswordResetRequest::generate(new Account($account_id), $this->nonce);
        $this->assertEquals($this->nonce, $req->nonce); // Create
        $req = PasswordResetRequest::match($this->nonce); // Match
        $this->assertEquals($this->nonce, $req->nonce);
    }

    public function testGeneratedResetCanBeFoundByAccount(){
        $req = PasswordResetRequest::generate($this->account);
        $req_dup = PasswordResetRequest::where('_account_id', '=', $this->account->id())->first();
        $this->assertEquals($req->id(), $req_dup->id());
    }

    // Reject resets that don't contain a new password
    /**
     * @group early
     */
    public function testRejectionOfResetsThatDontHaveANewPassword(){
        $this->assertFalse(PasswordResetRequest::reset($this->account, null));
    }

    // Reject resets that don't have a valid account_id
    /**
     * @group early
     */
    public function testRejectionOfResetsThatDontHaveAValidAccountId(){
        $account = new Account(1234567890);
        $this->assertFalse(PasswordResetRequest::reset($account, 'some_valid_password', $debug_email=false));
    }

    /**
     * @group early
     */
    public function testResetOfPasswordWhenCorrectDataGiven(){
        PasswordResetRequest::generate($this->account, $this->nonce='77766557777');
        $this->assertTrue(PasswordResetRequest::reset($this->account, 'some_password%##$@#', $debug_email=false));
    }

    public function testResetOfPasswordWhenCorrectDataGivenWithAlternatePasswordUsage(){
        PasswordResetRequest::generate($this->account, $this->nonce = '776543777');
        $this->assertTrue(PasswordResetRequest::reset($this->account, 'SDGAERHQEW$$%Y$%', $debug_email=false));
    }

    public function testMatchingARequestGetsYouAMatchingEmail(){
        $account_id = TestAccountCreateAndDestroy::account_id();
        $account = AccountFactory::findById($account_id);
        $reset = PasswordResetRequest::generate($account, $this->nonce='7778987777', $debug_email=false);
        $req = PasswordResetRequest::match($this->nonce);
        $final_account = new Account($req->_account_id);
        $this->assertNotEmpty($final_account->getActiveEmail());
        $this->assertEquals($account->getActiveEmail(), $final_account->getActiveEmail());
    }

    public function testSendingOfANotificationAfterResetOccurrs(){
        $account_id = TestAccountCreateAndDestroy::account_id();
        $account = AccountFactory::findById($account_id);
        $reset = PasswordResetRequest::generate($account, $this->nonce='7778987777', $debug_email=false);
        $req = PasswordResetRequest::match($this->nonce);
        $final_account = new Account($req->_account_id);
        $this->assertNotEmpty($final_account->getActiveEmail());
        $sent = PasswordResetRequest::sendResetNotification($final_account->getActiveEmail(), $debug_allowed=false);
        $this->assertTrue($sent);
    }


}

