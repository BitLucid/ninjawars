<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use NinjaWars\core\control\NinjamasterController;
use NinjaWars\core\extensions\SessionFactory;

class NinjamasterControllerTest extends NWTest {
	function setUp() {
        parent::setUp();
        // Mock the post request.
		SessionFactory::init(new MockArraySessionStorage());
	}

	function tearDown() {
        parent::tearDown();
        $session = SessionFactory::getSession();
        $session->invalidate();
    }

    public function testSuccessfulInstantiation() {
        $controller = new NinjamasterController();
        $this->assertInstanceOf(NinjamasterController::class, $controller);
    }

    public function testIndexRedirects() {
        $cont = new NinjamasterController();
        $redir = $cont->index($this->m_dependencies);
        $this->assertInstanceOf(RedirectResponse::class, $redir);
    }

    public function testToolsRedirect() {
        $cont = new NinjamasterController();
        $redir = $cont->tools($this->m_dependencies);
        $this->assertInstanceOf(RedirectResponse::class, $redir);
    }

    public function testPlayerTagsRedirect() {
        $cont = new NinjamasterController();
        $redir = $cont->player_tags($this->m_dependencies);
        $this->assertInstanceOf(RedirectResponse::class, $redir);
    }
}
