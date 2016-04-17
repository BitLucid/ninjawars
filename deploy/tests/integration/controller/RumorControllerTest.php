<?php
use NinjaWars\core\control\RumorController;

class RumorControllerTest extends PHPUnit_Framework_TestCase {
    public function testIndex() {
        $controller = new RumorController();
        $response = $controller->index();
        $reflection = new \ReflectionProperty(get_class($response), 'template');
        $reflection->setAccessible(true);
        $response_template = $reflection->getValue($response);
        $this->assertEquals('duel.tpl', $response_template);
    }
}
