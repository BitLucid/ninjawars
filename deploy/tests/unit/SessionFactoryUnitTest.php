<?php
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use NinjaWars\core\control\SessionFactory;

class SessionFactoryUnitTest extends PHPUnit_Framework_TestCase {
    public function testInit() {
		$session = SessionFactory::init(new MockArraySessionStorage());
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Session\Session', $session);
    }

    public function testGetSession() {
		$session = SessionFactory::init(new MockArraySessionStorage());
        $this->assertSame($session, SessionFactory::getSession());
    }
}
