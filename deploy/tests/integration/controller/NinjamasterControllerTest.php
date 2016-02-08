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

    public function testNinjamasterControllerCanBeInstantiatedWithoutError() {
        $controller = new NinjamasterController();
        $this->assertInstanceOf('NinjaWars\core\control\NinjamasterController', $controller);
    }

    public function testNinjamasterIndexRedirects(){
        $cont = new NinjamasterController();
        $redir = $cont->index();
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\RedirectResponse', $redir);
    }

    public function testNinjamasterMethodsRedirect(){
        $cont = new NinjamasterController();
        $redir = $cont->tools();
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\RedirectResponse', $redir);
        $redir = $cont->player_tags();
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\RedirectResponse', $redir);
    }
}
