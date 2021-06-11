<?php
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use NinjaWars\core\extensions\SessionFactory;
use NinjaWars\core\extensions\NWTemplate;

class NWTemplateUnitTest extends NWTest {
    public function setUp():void {
      parent::setUp();
		  SessionFactory::init(new MockArraySessionStorage());
    }

    public function tearDown():void {
      SessionFactory::annihilate();
      parent::tearDown();
    }

    public function testCustomConstructor() {
        $view = new NWTemplate();
        $this->assertInstanceOf('NinjaWars\core\extensions\NWTemplate', $view);
    }
}
