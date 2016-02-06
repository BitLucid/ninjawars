<?php
namespace tests\integration\controller;

use NinjaWars\core\control\NpcController;
use NinjaWars\core\control\SessionFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use \TestAccountCreateAndDestroy as TestAccountCreateAndDestroy;
use \PHPUnit_Framework_TestCase as PHPUnit_Framework_TestCase;

class NpcControllerTest extends PHPUnit_Framework_TestCase {
    function setUp() {
		SessionFactory::init(new MockArraySessionStorage());

        $this->char = TestAccountCreateAndDestroy::char();

        $this->controller = new NpcController([
            'randomness' => function(){return 0;}
        ]);
    }

    function tearDown() {
        TestAccountCreateAndDestroy::destroy();
    }

    function testControllerIndexDoesntError() {
        $response = $this->controller->index();
        $this->assertNotEmpty($response);
    }

    function testControllerGetRandomness() {
        $this->controller = new NpcController([
            'char_id'    => ($this->char->id()),
            'randomness' => function(){return 0;}
        ]);

        $response = $this->controller->index();
        $this->assertNotEmpty($response);
    }

    function testControllerAttackAsIfAgainstAPeasant() {
        $_SERVER['REQUEST_URI'] = '/npc/attack/peasant2';
        $response = $this->controller->attack();
        $this->assertNotEmpty($response);
    }
}
