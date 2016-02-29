<?php
use NinjaWars\core\control\RumorController;

class RumorControllerTest extends PHPUnit_Framework_TestCase {
    public function testIndex() {
        $controller = new RumorController();
        $result = $controller->index();
        $this->assertEquals('duel.tpl', $result['template']);
    }
}
