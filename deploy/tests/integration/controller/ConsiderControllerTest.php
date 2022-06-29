<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\HttpFoundation\RedirectResponse;
use NinjaWars\core\environment\RequestWrapper;
use NinjaWars\core\extensions\SessionFactory;
use NinjaWars\core\extensions\StreamedViewResponse;
use NinjaWars\core\control\ConsiderController;

class ConsiderControllerTest extends NWTest
{
    private $controller;

    public function setUp(): void
    {
        parent::setUp();
        $this->controller = new ConsiderController();
        SessionFactory::init(new MockArraySessionStorage());
        $char_id = TestAccountCreateAndDestroy::create_testing_account();
        SessionFactory::getSession()->set('player_id', $char_id);
    }

    public function tearDown(): void
    {
        RequestWrapper::destroy();
        $session = SessionFactory::getSession();
        $session->invalidate();
        parent::tearDown();
    }

    public function testIndex()
    {
        $response = $this->controller->index($this->m_dependencies);

        $this->assertInstanceOf(StreamedViewResponse::class, $response);
    }

    public function testNextEnemy()
    {
        $this->markTestSkipped('Not working in ci with fixture data');
        $response = $this->controller->nextEnemy($this->m_dependencies);

        $this->assertInstanceOf(StreamedViewResponse::class, $response);
    }


    public function testAddBlankEnemy()
    {
        $response = $this->controller->addEnemy($this->m_dependencies);

        $this->assertInstanceOf(RedirectResponse::class, $response);
    }
}
