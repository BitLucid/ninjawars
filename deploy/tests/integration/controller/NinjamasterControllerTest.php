<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use NinjaWars\core\control\NinjamasterController;
use NinjaWars\core\extensions\SessionFactory;

class NinjamasterControllerTest extends PHPUnit_Framework_TestCase {
	function setUp() {
        // Mock the post request.
		SessionFactory::init(new MockArraySessionStorage());
	}

	function tearDown() {
        $session = SessionFactory::getSession();
        $session->invalidate();
    }

    public function testSuccessfulInstantiation() {
        $controller = new NinjamasterController();
        $this->assertInstanceOf(NinjamasterController::class, $controller);
    }

    public function testIndexRedirects() {
        $cont = new NinjamasterController();
        $redir = $cont->index();
        $this->assertInstanceOf(RedirectResponse::class, $redir);
    }

    public function testToolsRedirect() {
        $cont = new NinjamasterController();
        $redir = $cont->tools();
        $this->assertInstanceOf(RedirectResponse::class, $redir);
    }

    public function testPlayerTagsRedirect() {
        $cont = new NinjamasterController();
        $redir = $cont->player_tags();
        $this->assertInstanceOf(RedirectResponse::class, $redir);
    }
}
