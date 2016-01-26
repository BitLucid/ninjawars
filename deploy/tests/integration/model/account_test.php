<?php
require_once(ROOT.'tests/TestAccountCreateAndDestroy.php');
require_once(ROOT.'core/data/AccountFactory.php');
require_once(ROOT.'core/data/Account.php');

class Account_Test extends PHPUnit_Framework_TestCase {

	var $testAccountId;

	public function setUp(){
		$_SERVER['REMOTE_ADDR']=isset($_SERVER['REMOTE_ADDR'])? $_SERVER['REMOTE_ADDR'] : '127.0.0.1';
		$this->test_email = TestAccountCreateAndDestroy::$test_email;
		$this->test_password = TestAccountCreateAndDestroy::$test_password;
		$this->test_ninja_name = TestAccountCreateAndDestroy::$test_ninja_name;
		TestAccountCreateAndDestroy::purge_test_accounts(TestAccountCreateAndDestroy::$test_ninja_name);
		$this->testAccountId = TestAccountCreateAndDestroy::account_id();
	}

	public function tearDown(){
		TestAccountCreateAndDestroy::purge_test_accounts();
	}

	public function testCreatingAnAccount(){
		$account_id = $this->testAccountId;
		$acc = new Account($account_id);
		$this->assertTrue($acc instanceof Account);
		$this->assertNotEmpty($acc->getIdentity());
	}

	public function testAccountHasIdentity(){
		$account = new Account($this->testAccountId);
		$this->assertNotEmpty($account->getIdentity());
	}

	public function testAccountHasAType(){
		$account = new Account($this->testAccountId);
		$this->assertTrue(gettype($account->getType()) === 'integer');
	}

	public function testAccountHasAnId(){
		$account = new Account($this->testAccountId);
		$this->assertGreaterThan(0, $account->getId());
	}

	public function testAccountFactoryReturnsAccount(){
		$account = AccountFactory::make($this->testAccountId);
		$this->assertTrue($account instanceof Account);
		$this->assertNotEmpty($account->getIdentity());
	}

	public function testAccountFactoryReturnsAccountWithMatchingIdentity(){
		$identity = $this->test_email;
		$acc = AccountFactory::findByIdentity($identity);
		$this->assertEquals($identity, $acc->getIdentity());
	}

	public function testAccountHasActiveEmail(){
		$account = AccountFactory::make($this->testAccountId);
		$this->assertNotEmpty($account->getActiveEmail());
	}

	public function testAccountCanHaveOauthAddedInMemory(){
		$account = AccountFactory::make($this->testAccountId);
		$oauth_id = 88888888888888;
		$account->setOauthId($oauth_id, 'facebook');
		$this->assertEquals($oauth_id, $account->getOauthId());
	}

	public function testAccountCanSaveNewOauthIdAfterHavingItAdded(){
		$account = AccountFactory::make($this->testAccountId);
		$oauth_id = 88888888888888;
		$account->setOauthId($oauth_id, 'facebook');
		AccountFactory::save($account);
		$account_dupe = AccountFactory::make($this->testAccountId);
		$this->assertEquals($oauth_id, $account_dupe->getOauthId());
	}

	public function testAccountPasswordCanBeChanged(){
		$account = AccountFactory::make($this->testAccountId);
		$updated = $account->changePassword('whatever gibberish');
		$this->assertTrue((bool)$updated);
	}



}