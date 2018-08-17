<?php
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use NinjaWars\core\extensions\SessionFactory;

class SessionFactoryUnitTest extends NWTest {
    private $session;

    public function testInit() {
        SessionFactory::annihilate();
        $this->session = SessionFactory::init(new MockArraySessionStorage());
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Session\Session', $this->session);
        $this->session->invalidate();
    }

    public function testGetSession() {
        $this->session = SessionFactory::init(new MockArraySessionStorage());
        $this->assertSame($this->session, SessionFactory::getSession());
        $this->session->invalidate();
    }
}
