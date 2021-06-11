<?php
use NinjaWars\core\control\MapController;
use NinjaWars\core\extensions\StreamedViewResponse;

class MapControllerUnitTest extends NWTest {
    private $controller;

    public function setUp():void {
        parent::setUp();
        $this->controller = new MapController();
    }

    public function tearDown():void {
        parent::tearDown();
    }

    public function testIndex() {
        $response = $this->controller->index($this->m_dependencies);

        $this->assertInstanceOf(StreamedViewResponse::class, $response);
    }
}
