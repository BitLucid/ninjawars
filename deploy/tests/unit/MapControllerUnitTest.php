<?php
use NinjaWars\core\control\MapController;

class MapControllerUnitTest extends PHPUnit_Framework_TestCase {
    private $controller;

    public function __construct() {
    }

    protected function setUp() {
        $this->controller = new MapController();
    }

    protected function tearDown() {
    }

    public function testIndex() {
        $response = $this->controller->index();

        $this->assertArrayHasKey('template', $response);
    }
}
