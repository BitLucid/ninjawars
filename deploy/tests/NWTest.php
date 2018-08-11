<?php
use Pimple\Container;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use NinjaWars\core\extensions\SessionFactory;
use NinjaWars\core\data\Account;
use NinjaWars\core\data\Player;
use NinjaWars\core\environment\RequestWrapper;
use Symfony\Component\HttpFoundation\Request;

class NWTest extends PHPUnit_Framework_TestCase {
    protected $m_dependencies;

    /**
     */
    public function setUp() {
        $this->m_dependencies = new Container();

        $this->m_dependencies['session'] = function($c) {
            return SessionFactory::getSession();
        };

        $this->m_dependencies['current_player'] = $this->m_dependencies->factory(function ($c) {
            return Player::find(SessionFactory::getSession()->get('player_id'));
        });
    }

    /**
     * If you want to test the logged out state, have dependencies without the current_player.
     */
    public function mockLogout(): Container{
        SessionFactory::getSession()->invalidate();
        RequestWrapper::inject(new Request());
        return new Container();
    }

    public function tearDown() {
        $this->m_dependences = null;
    }

    /**
     * Create a mock login, with real created account and character
     */
    public function login(){
        SessionFactory::init(new MockArraySessionStorage());
        $this->char = TestAccountCreateAndDestroy::char();
        SessionFactory::getSession()->set('authenticated', true);
        $this->account = Account::findByChar($this->char);
        SessionFactory::getSession()->set('account_id', $this->account->id());
    }

    /**
     * Destroy the mock login.
     */
    public function loginTearDown(){
        $session = SessionFactory::getSession();
        $session->invalidate();
        RequestWrapper::inject(new Request());
        TestAccountCreateAndDestroy::purge_test_accounts();
    }
}
