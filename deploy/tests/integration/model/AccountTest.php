<?php

use NinjaWars\core\data\Account;
use NinjaWars\core\data\Player;

/** SEE ALSO AccountConfTest as it has confirmation tests */

class AccountTest extends NWTest
{
    public $testAccountId;
    private $extra_char_name;
    private $extra_char_email;
    private $char;
    private $char2;
    private $test_email;
    private $test_password;
    private $test_ninja_name;

    public function setUp(): void
    {
        parent::setUp();
        TestAccountCreateAndDestroy::destroy();
        $this->extra_char_name = 'some_extra_test_char';
        $this->extra_char_email = 'temp_account_test_phpunit@example.com';
        TestAccountCreateAndDestroy::destroy($this->extra_char_name, $this->extra_char_email);
        $this->char = TestAccountCreateAndDestroy::char();
        $this->char2 = TestAccountCreateAndDestroy::char_2();
        $_SERVER['REMOTE_ADDR'] = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1';
        $this->test_email = TestAccountCreateAndDestroy::$test_email;
        $this->test_password = TestAccountCreateAndDestroy::$test_password;
        $this->test_ninja_name = TestAccountCreateAndDestroy::$test_ninja_name;
        $this->testAccountId = query_item("SELECT account_id FROM accounts WHERE account_identity = :email", [':email' => $this->test_email]);
    }

    public function tearDown(): void
    {
        TestAccountCreateAndDestroy::destroy();
        TestAccountCreateAndDestroy::destroy($this->extra_char_name);
        parent::tearDown();
    }

    public function testCanObtainPreExistingAccountById()
    {
        $account = Account::findById($this->testAccountId);
        $this->assertNotNull($account, 'Account::findById() failed to find pre-existing account');
        $this->assertEquals($this->testAccountId, $account->id());
    }


    public function testCanObtainPreExistingAccountByCharacter()
    {
        $account = Account::findByChar($this->char);
        $this->assertNotEmpty(Player::find($this->char->id()), 'Player::find failed to find pre-existing account');
        $this->assertNotNull($account, 'Account::findByCharacter() failed to find pre-existing account');
    }


    public function testAccountOperationalHasValue()
    {
        $account_id = $this->testAccountId;
        $account = Account::findById($account_id);
        $this->assertNotEmpty($account, 'No initial account was created');
        $this->assertGreaterThan(0, $account->id());
        $this->assertTrue($account->isOperational(), 'Account::operational() returned false');
    }

    public function testAccountSetOperationalCanChange()
    {
        $account = Account::findById($this->testAccountId);
        $this->assertNotEmpty($account, 'No initial account was created');
        $account->setOperational(false);
        $account->save();
        $final_account = Account::findById($this->testAccountId);
        $this->assertFalse($final_account->isOperational(), 'Account::setOperational() failed to change operational status');
    }

    public function testAccountDeactivate()
    {
        $account = Account::findById($this->testAccountId);
        $this->assertNotEmpty($account, 'No initial account was created');
        Account::deactivate($account);
        Account::deactivateByCharacter($this->char2);
        $account_f = Account::findById($account->id());
        $this->assertFalse($account_f->isOperational(), 'Account::setOperational() failed to change operational status');
    }

    public function testAccountReactivate()
    {
        $account = Account::findById($this->testAccountId);
        Account::deactivate($account);
        $this->assertFalse((Account::findById($this->testAccountId))->isOperational(), 'Account::deactivate() failed to change operational status');
        $reactivated = Account::activate(Account::findById($this->testAccountId));
        $this->assertGreaterThan(0, $reactivated, 'Expected some account to be reactivated');
        $accountF = Account::findById($this->testAccountId);
        $this->assertTrue($accountF->isOperational(), 'Account::reactivateByCharacter() failed to change operational status');
    }

    public function testCreatingAnAccount()
    {
        $account_id = $this->testAccountId;
        $acc = Account::findById($account_id);
        $this->assertTrue($acc instanceof Account);
        $this->assertNotEmpty($acc->getIdentity());
    }

    public function testAccountHasIdentity()
    {
        $account = Account::findById($this->testAccountId);
        $this->assertNotEmpty($account->getIdentity());
    }

    public function testAccountHasAType()
    {
        $account = Account::findById($this->testAccountId);
        $this->assertTrue(gettype($account->getType()) === 'integer');
    }

    public function testAccountHasAnId()
    {
        $account = Account::findById($this->testAccountId);
        $this->assertGreaterThan(0, $account->getId());
    }

    public function testAccountReturnsAccount()
    {
        $account = Account::findById($this->testAccountId);
        $this->assertTrue($account instanceof Account);
        $this->assertNotEmpty($account->getIdentity());
    }

    public function testAccountReturnsAccountWithMatchingIdentity()
    {
        $identity = $this->test_email;
        $acc = Account::findByIdentity($identity);
        $this->assertEquals($identity, $acc->getIdentity());
    }

    public function testAccountHasActiveEmail()
    {
        $account = Account::findById($this->testAccountId);
        $this->assertNotEmpty($account->getActiveEmail());
    }

    public function testAccountCanHaveOauthAddedInMemory()
    {
        $account = Account::findById($this->testAccountId);
        $oauth_id = 88888888888888;
        $account->setOauthId($oauth_id, 'facebook');
        $this->assertEquals($oauth_id, $account->getOauthId());
    }

    public function testSetAndGetOauthProvider()
    {
        $account = new Account();
        $account->setOauthProvider('facebook');
        $this->assertEquals('facebook', $account->getOauthProvider());
    }

    public function testAccountCanSaveNewOauthIdAfterHavingItAdded()
    {
        $account = Account::findById($this->testAccountId);
        $oauth_id = 88888888888888;
        $account->setOauthId($oauth_id, 'facebook');
        $account->save();
        $account_dupe = Account::findById($this->testAccountId);
        $this->assertEquals($oauth_id, $account_dupe->getOauthId());
    }

    public function testAccountPasswordCanBeChanged()
    {
        $account = Account::findById($this->testAccountId);
        $updated = $account->changePassword('whatever gibberish');
        $this->assertTrue((bool)$updated);
    }

    public function testFindAccountByEmail()
    {
        $account = Account::findById($this->testAccountId);
        $account2 = Account::findByEmail($account->email());
        $this->assertEquals($account->id(), $account2->id());
    }

    public function testFindAccountByEmailWithEmptyInput()
    {
        $account = Account::findByEmail('   ');
        $this->assertNull($account);
    }

    public function testFindAccountByNinja()
    {
        $player = Player::findByName($this->test_ninja_name);
        $account = Account::findByChar($player);
        $this->assertNotNull($account);
    }

    public function testFindAccountByNinjaName()
    {
        $account = Account::findByNinjaName($this->test_ninja_name);
        $this->assertNotNull($account);
    }

    public function testFindAccountByNonexistentId()
    {
        $account = Account::findById(-120);
        $this->assertNull($account);
    }

    public function testThanAccountCanBeSetAsDifferentType()
    {
        $account = new Account();
        $account->setType(2);
        $this->assertEquals(2, $account->type);
    }

    // @see accountConfTest also as it has confirmation tests 
    public function testAccountCanBeConfirmed()
    {
        $account = Account::findById($this->testAccountId);
        $account->setConfirmed(true);
        $this->assertTrue($account->isConfirmed());
    }

    public function testAccountCanBeUnConfirmed()
    {
        $account = Account::findById($this->testAccountId);
        $account->setConfirmed(0);
        $this->assertTrue($account->isConfirmed());
    }

    public function testAuthenticationOfAccountWithNoDatabaseAnalogFails()
    {
        $account = new Account();
        $this->assertFalse($account->authenticate('an invalid password'));
    }

    public function testAccountCanHavePlayers()
    {
        $account = Account::findByNinjaName($this->test_ninja_name);
        $pcs = $account->getCharacters();
        $this->assertNotEmpty($pcs);
        $this->assertInstanceOf(Player::class, reset($pcs));
    }

    public function testAccountPlayerCanBeDeactivated()
    {
        $account = Account::findByNinjaName($this->test_ninja_name);
        $pcs = $account->getCharacters();
        $pc = reset($pcs);
        Account::deactivateSingleCharacter($pc);
        $updated_pc = Player::find($pc->id());
        $this->assertFalse($updated_pc->isActive());
    }

    public function testAccountPlayerCanBeReactivated()
    {
        $account = Account::findByNinjaName($this->test_ninja_name);
        $pcs = $account->getCharacters();
        $pc = reset($pcs);
        Account::deactivateSingleCharacter($pc);
        $updated_pc = Player::find($pc->id());
        $this->assertFalse($updated_pc->isActive());
        Account::reactivateSingleCharacter($pc);
        $updated_pc = Player::find($pc->id());
        $this->assertTrue($updated_pc->isActive());
    }
}
