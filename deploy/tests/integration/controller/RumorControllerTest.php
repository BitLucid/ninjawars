<?php
use NinjaWars\core\control\RumorController;

class RumorControllerTest extends NWTest {
    public function testIndex() {
        $controller = new RumorController();
        $response = $controller->index($this->m_dependencies);
        $reflection = new \ReflectionProperty(get_class($response), 'template');
        $reflection->setAccessible(true);
        $response_template = $reflection->getValue($response);
        $this->assertEquals('duel.tpl', $response_template);
    }

    public function testIndexIsRenderableEvenIfLoggedOut() {
        $controller = new RumorController();
        $response = $controller->index($this->mockLogout());
        $reflection = new \ReflectionProperty(get_class($response), 'template');
        $reflection->setAccessible(true);
        $response_template = $reflection->getValue($response);
        $this->assertEquals('duel.tpl', $response_template);
    }
}
