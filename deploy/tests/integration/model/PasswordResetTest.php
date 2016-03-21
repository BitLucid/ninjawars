<?php
use NinjaWars\core\data\PasswordResetRequest;
use NinjaWars\core\data\Account;

class PasswordResetTest extends PHPUnit_Framework_TestCase {
    function setUp() {
        $this->account_id = TestAccountCreateAndDestroy::account_id();
        $this->account = Account::findById($this->account_id);
        $this->nonce = null;
    }

    function tearDown() {
        // Don't use naked queries outside of a model layer elsewhere.
        query("delete from password_reset_requests where nonce = '777777' or nonce = '77778877' or nonce = '7777777' or nonce = :nonce or _account_id = :id",
            [':nonce'=>(isset($this->nonce)? $this->nonce : null), ':id'=>$this->account_id]);
        TestAccountCreateAndDestroy::purge_test_accounts();
        query("delete from password_reset_requests where nonce = '777777' or nonce = '77778877' or nonce = '7777777' or nonce = :nonce or _account_id = :id",
            [':nonce'=>(isset($this->nonce)? $this->nonce : null), ':id'=>$this->account_id]);
    }

    /**
     * @group first
     */
    public function testModelCreateARequestWithTheRawModelFunctionality() {
        $req = PasswordResetRequest::create(['_account_id'=>$this->account_id, 'nonce'=>'777666888']);
        $this->assertInstanceOf('NinjaWars\core\data\PasswordResetRequest', $req);
        $this->assertGreaterThan(0, (int) $req->nonce);
    }

    /**
     * Make sure a user can make a request
     *
     * @group early
     */
    public function testCreatePasswordResetEntry() {
        $request = PasswordResetRequest::generate($this->account);
        $this->assertTrue((bool)$request->nonce);
    }

    /**
     * Retrieve the password request with the appropriate nonce data
     *
     * @group early
     */
    public function testRetrieveCreatedPasswordReset() {
        $account_id = query_item('select account_id from accounts limit 1');
        $this->assertGreaterThan(0, $account_id);
        $this->nonce='777777';
        $req = PasswordResetRequest::generate(Account::findById($account_id), $this->nonce);
        $this->assertEquals($this->nonce, $req->nonce); // Create
        $req = PasswordResetRequest::match($this->nonce); // Match
        $this->assertEquals($this->nonce, $req->nonce);
    }

    public function testGeneratedResetCanBeFoundByAccount() {
        $req = PasswordResetRequest::generate($this->account);
        $req_dup = PasswordResetRequest::where('_account_id', '=', $this->account->id())->first();
        $this->assertEquals($req->id(), $req_dup->id());
    }

    /**
     * Reject resets that don't contain a new password
     *
     * @group early
     */
    public function testRejectionOfResetsThatDontHaveANewPassword() {
        $this->assertFalse(PasswordResetRequest::reset($this->account, null));
    }

    /**
     * Reject resets that don't have a valid account_id
     *
     * @group early
     */
    public function testRejectionOfResetsThatDontHaveAValidAccountId() {
        $account = new Account([]);
        $this->assertFalse(PasswordResetRequest::reset($account, 'some_valid_password', false));
    }

    /**
     * @group early
     */
    public function testResetOfPasswordWhenCorrectDataGiven() {
        PasswordResetRequest::generate($this->account, $this->nonce='77766557777');
        $this->assertTrue(PasswordResetRequest::reset($this->account, 'some_password%##$@#', false));
    }

    public function testResetOfPasswordWhenCorrectDataGivenWithAlternatePasswordUsage() {
        PasswordResetRequest::generate($this->account, $this->nonce = '776543777');
        $this->assertTrue(PasswordResetRequest::reset($this->account, 'SDGAERHQEW$$%Y$%', false));
    }

    public function testMatchingARequestGetsYouAMatchingEmail() {
        $account_id = TestAccountCreateAndDestroy::account_id();
        $account = Account::findById($account_id);
        PasswordResetRequest::generate($account, $this->nonce='7778987777', false);
        $req = PasswordResetRequest::match($this->nonce);
        $final_account = $req->account();
        $this->assertNotEmpty($final_account->getActiveEmail());
        $this->assertEquals($account->getActiveEmail(), $final_account->getActiveEmail());
    }

    public function testSendingOfANotificationAfterResetOccurrs() {
        $account_id = TestAccountCreateAndDestroy::account_id();
        $account = Account::findById($account_id);
        PasswordResetRequest::generate($account, $this->nonce='7778987777', false);
        $req = PasswordResetRequest::match($this->nonce);
        $final_account = $req->account();
        $this->assertNotEmpty($final_account->getActiveEmail());
        $sent = PasswordResetRequest::sendResetNotification($final_account->getActiveEmail(), false);
        $this->assertTrue($sent);
    }

    public function testPerformingAResetInvalidatesUsedRequest(){
        $account_id = TestAccountCreateAndDestroy::account_id();
        $account = Account::findById($account_id);
        PasswordResetRequest::generate($account, $this->nonce='77warkwark', false);
        PasswordResetRequest::reset($account, 'new_pass34532');
        $req = PasswordResetRequest::match($this->nonce);
        $this->assertEmpty($req); // Request shouldn't match because it should already be used.
    }
}
