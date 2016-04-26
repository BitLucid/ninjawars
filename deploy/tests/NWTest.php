<?php
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use NinjaWars\core\extensions\SessionFactory;
use NinjaWars\core\data\Account;

class NWTest extends PHPUnit_Framework_TestCase{

    /**
     * Create a mock login, with real created account and character
     **/
    public function login(){
        SessionFactory::init(new MockArraySessionStorage());
        $this->char = TestAccountCreateAndDestroy::char();
        SessionFactory::getSession()->set('authenticated', true);
        $this->account = Account::findByChar($this->char);
        SessionFactory::getSession()->set('account_id', $this->account->id());
    }

    /**
     * Destroy the mock login.
     **/
    public function loginTearDown(){
        $session = SessionFactory::getSession();
        $session->invalidate();
        TestAccountCreateAndDestroy::purge_test_accounts();
    }
}