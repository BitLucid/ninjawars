<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use NinjaWars\core\environment\RequestWrapper;
use NinjaWars\core\extensions\SessionFactory;
use NinjaWars\core\data\Account;

class AccountTest extends NWTest
{

    public function setUp(): void
    {
        parent::setUp();
        $this->char = TestAccountCreateAndDestroy::char();
        $this->char2 = TestAccountCreateAndDestroy::char_2();
    }

    public function tearDown(): void
    {
        TestAccountCreateAndDestroy::purge_test_accounts();
        parent::tearDown();
    }

    public function testAccountOperationalHasValue()
    {
        $account = Account::findByChar($this->char);
        $this->assertTrue($account->isOperational(), 'Account::operational() returned false');
    }

    public function testAccountSetOperationalCanChange()
    {
        $account = Account::findByChar($this->char);
        $account->setOperational(false);
        $this->assertFalse($account->isOperational(), 'Account::setOperational() failed to change operational status');
    }

    public function testAccountDeactivate()
    {
        $account = Account::findByChar($this->char);
        Account::deactivate($account);
        Account::deactivateByCharacter($this->char2);
        $accountF = Account::findByChar($this->char);
        $this->assertFalse($accountF->isOperational(), 'Account::setOperational() failed to change operational status');
    }

    public function testAccountReactivate()
    {
        $account = Account::findByChar($this->char);
        Account::deactivate($account);
        $this->assertFalse((Account::findByChar($this->char))->isOperational(), 'Account::deactivate() failed to change operational status');
        Account::reactivateByCharacter($this->char);
        $accountF = Account::findByChar($this->char);
        $this->assertTrue($accountF->isOperational(), 'Account::reactivateByCharacter() failed to change operational status');
    }
}
