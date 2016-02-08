<?php
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use NinjaWars\core\extensions\SessionFactory;

class SessionFactoryUnitTest extends PHPUnit_Framework_TestCase {
    private $session;

    public function setUp() {
        $this->session = SessionFactory::init(new MockArraySessionStorage());
    }

    public function tearDown() {
        $session = SessionFactory::getSession();
        $session->invalidate();
    }

    public function testInit() {
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Session\Session', $this->session);
    }

    public function testGetSession() {
        $this->assertSame($this->session, SessionFactory::getSession());
    }
}
