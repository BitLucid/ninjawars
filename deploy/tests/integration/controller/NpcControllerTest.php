<?php
namespace tests\integration\controller;
require_once(ROOT.'tests/TestAccountCreateAndDestroy.php');

use NinjaWars\core\control\NpcController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use \TestAccountCreateAndDestroy as TestAccountCreateAndDestroy;
use \PHPUnit_Framework_TestCase as PHPUnit_Framework_TestCase;


/**
 * Mock of the session, just returns 500 for everything for now.
 */
class MockSession{
    /**
     * Echoing mock
     */
    public function get($x){
        return 500;
    }

    /**
     * Noop mock
     */
    public function set(){
    }
}

class NpcControllerTest extends PHPUnit_Framework_TestCase {
    function setUp() {
        $this->char = TestAccountCreateAndDestroy::char();
        $mock_session = new MockSession;
        $this->controller = new NpcController([
            'session'=>$mock_session, 
            'char_id'=>($this->char->id()), 
            'randomness'=>function(){return 0;}
            ]);
    }

    function tearDown() {
        TestAccountCreateAndDestroy::destroy();
    }

    function testControllerIndexDoesntError(){
        $response = $this->controller->index();
        $this->assertNotEmpty($response);
    }

    function testControllerGetRandomness(){
        $this->controller = new NpcController([
            'session'=>$mock_session, 
            'char_id'=>($this->char->id()), 
            'randomness'=>function(){return 0;}
            ]);
        $response = $this->controller->index();
        $this->assertNotEmpty($response);
    }

    function testControllerAttackAsIfAgainstAPeasant(){
        $_SERVER['REQUEST_URI'] = '/npc/attack/peasant2';
        $response = $this->controller->attack();
        $this->assertNotEmpty($response);
    }



}
