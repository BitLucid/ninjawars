<?php
namespace tests\integration\controller;

use NinjaWars\core\data\Npc;
use NinjaWars\core\control\NpcController;
use NinjaWars\core\extensions\SessionFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use \TestAccountCreateAndDestroy;
use \PHPUnit_Framework_TestCase;
use \Player;

class NpcControllerTest extends PHPUnit_Framework_TestCase {
    protected function setUp() {
        $this->char = TestAccountCreateAndDestroy::char();
        SessionFactory::init(new MockArraySessionStorage());
        SessionFactory::getSession()->set('player_id', $this->char->id());
        $this->controller = new NpcController([
            'randomness' => function(){return 0;}
        ]);
    }

    protected function tearDown() {
        TestAccountCreateAndDestroy::destroy();
        $session = SessionFactory::getSession();
        $session->invalidate();
    }

    public function testControllerIndexDoesntError() {
        $response = $this->controller->index();
        $this->assertNotEmpty($response);
    }

    public function testControllerGetRandomnessDoesntError() {
        $this->controller = new NpcController([
            'char_id'    => ($this->char->id()),
            'randomness' => function(){return 0;}
        ]);

        $response = $this->controller->index();
        $this->assertNotEmpty($response);
    }

    public function testSessionHasPlayerId(){
        $this->assertEquals($this->char->id(), SessionFactory::getSession()->get('player_id'));
    }

    public function testControllerAttackAsIfAgainstAPeasant() {
        $_SERVER['REQUEST_URI'] = '/npc/attack/peasant';
        $response = $this->controller->attack();
        $this->assertNotEmpty($response);
        $this->assertEquals('peasant', $response['parts']['victim']);
    }

    public function testAttackPeasantWithABountableHighLevelCharacter() {
        $_SERVER['REQUEST_URI'] = '/npc/attack/peasant';
        // Bump the test player's level for bounty purposes.
        $this->char->vo->level = 20;
        $this->char->save();
        $response = $this->controller->attack();
        $this->assertNotEmpty($response);
        $final_char = Player::find($this->char->id());
        $this->assertGreaterThan(0, $final_char->bounty());
    }

    public function testControllerAttackAsIfAgainstAPeasant2() {
        $_SERVER['REQUEST_URI'] = '/npc/attack/peasant2';
        $response = $this->controller->attack();
        $final_char = Player::find($this->char->id());
        $this->assertNotEmpty($response);
        $this->assertEquals('peasant2', $response['parts']['victim']);
        $this->assertGreaterThan(0, $final_char->health());
    }

    public function testControllerAttackAsIfAgainstAMerchant() {
        $_SERVER['REQUEST_URI'] = '/npc/attack/merchant';
        $response = $this->controller->attack();
        $this->assertEquals('merchant', $response['parts']['victim']);
    }

    public function testControllerAttackAsIfAgainstAMerchant2() {
        $_SERVER['REQUEST_URI'] = '/npc/attack/merchant2';
        $init_gold = $this->char->gold();
        $npco = new Npc('merchant2');
        $response = $this->controller->attack();
        $final_char = Player::find($this->char->id());
        $this->assertNotEmpty($response);
        $this->assertEquals('merchant2', $response['parts']['victim']);
        $this->assertGreaterThan(0, $npco->min_gold());
        $this->assertGreaterThan($init_gold, $final_char->gold());
    }

    public function testControllerAttackAsIfAgainstAGuard() {
        $_SERVER['REQUEST_URI'] = '/npc/attack/guard';
        $response = $this->controller->attack();
        $this->assertNotEmpty($response);
        $this->assertEquals('guard', $response['parts']['victim']);
    }

    public function testControllerAttackAsIfAgainstAGuard2() {
        $_SERVER['REQUEST_URI'] = '/npc/attack/guard2';
        $response = $this->controller->attack();
        $this->assertNotEmpty($response);
        $this->assertEquals('guard2', $response['parts']['victim']);
    }

    public function testControllerAttackAsIfAgainstAThief() {
        $_SERVER['REQUEST_URI'] = '/npc/attack/thief';
        $response = $this->controller->attack();
        $this->assertEquals('thief', $response['parts']['victim']);
    }

    public function testControllerAttackAsIfAgainstAThief2() {
        $this->markTestIncomplete('There is not yet a thief2, but turn this on when there is.');
        $_SERVER['REQUEST_URI'] = '/npc/attack/thief2';
        $response = $this->controller->attack();
        $this->assertEquals('theif2', $response['parts']['victim']);
    }

    public function testControllerAttackAgainstSamurai() {
        $this->char->vo->kills = 40;
        $this->char->vo->level = 5;
        $this->char->vo->strength = 25;
        $this->char->addStatus(STEALTH);
        $this->char->save();

        $_SERVER['REQUEST_URI'] = '/npc/attack/samurai';
        $response = $this->controller->attack();
        $this->assertEquals('samurai', $response['parts']['victim']);
    }

    public function testControllerFailedAttackAgainstSamurai() {
        $_SERVER['REQUEST_URI'] = '/npc/attack/samurai';
        $response = $this->controller->attack();
        $this->assertEquals('samurai', $response['parts']['victim']);
    }

    public function testRandomEncounter() {
        $this->controller = new NpcController([
            'randomness' => function(){ return 1; }
        ]);

        $_SERVER['REQUEST_URI'] = '/npc/attack/peasant';
        $response = $this->controller->attack();
        $this->assertNotEmpty($response);
    }
}
