<?php
require_once(CORE.'/extensions/lib_templates.php');
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use NinjaWars\core\extensions\SessionFactory;
use NinjaWars\core\extensions\NWTemplate;

class NWTemplateUnitTest extends PHPUnit_Framework_TestCase {
    public function setUp() {
		SessionFactory::init(new MockArraySessionStorage());
    }

    public function tearDown() {
		SessionFactory::annihilate();
    }

    public function testAssignNull() {
        $template = new NWTemplate();
        $result = $template->assignArray(null);
        $this->assertNull($result);
    }
}
