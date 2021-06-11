<?php
namespace NinjaWars\tests\unit;

use \NWTest;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use NinjaWars\core\control\AbstractController;
use NinjaWars\core\extensions\StreamedViewResponse;
use NinjaWars\core\extensions\SessionFactory;
use NinjaWars\core\data\Player;

class LocalTestController extends AbstractController {
    const PRIV  = true;
    const ALIVE = true;
}

class AbstractControllerUnitTest extends NWTest {
	public function setUp(): void {
        parent::setUp();
		SessionFactory::init(new MockArraySessionStorage());
    }

	public function tearDown(): void {
        parent::tearDown();
    }

    public function testRenderDefaultError() {
        $c = new LocalTestController();
        $response = $c->renderDefaultError();
        $this->assertInstanceOf(StreamedViewResponse::class, $response);
    }

    public function testValidatePrivate() {
        $c = new LocalTestController();
        $error = $c->validate($this->m_dependencies);
        $this->assertEquals('log_in', $error);
    }

    public function testValidateFrozen() {
        $ninja = new Player();
        $ninja->addStatus(FROZEN);
        $this->m_dependencies['current_player'] = $ninja;
        SessionFactory::getSession()->set('authenticated', true);
        $c = new LocalTestController();
        $error = $c->validate($this->m_dependencies);
        $this->assertEquals('frozen', $error);
    }

    public function testValidateDead() {
        $ninja = new Player();
        $ninja->setHealth(0);
        SessionFactory::getSession()->set('authenticated', true);
        $this->m_dependencies['current_player'] = $ninja;
        $c = new LocalTestController();
        $error = $c->validate($this->m_dependencies);
        $this->assertEquals('dead', $error);
    }
}
