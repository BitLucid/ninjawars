<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\HttpFoundation\Response;
use NinjaWars\core\extensions\StreamedViewResponse;
use NinjaWars\core\control\NinjamasterController;
use NinjaWars\core\extensions\SessionFactory;
use NinjaWars\core\data\Player;

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

    public function testIndexForAnAdminToEnsureItLoadsAtAll() {
        $cont = new NinjamasterController();
        $player = Player::findByName('tchalvak');
        if(!$player){
            $this->markSkippedForMissingDependecy();
        }
        $this->m_dependencies['current_player'] = $player;
        $response = $cont->index($this->m_dependencies);
        $this->assertInstanceOf(Response::class, $response);
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
