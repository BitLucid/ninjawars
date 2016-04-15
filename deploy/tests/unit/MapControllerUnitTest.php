<?php
use NinjaWars\core\control\MapController;
use NinjaWars\core\extensions\StreamedViewResponse;

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

        $this->assertInstanceOf(StreamedViewResponse::class, $response);
    }
}
