<?php
namespace tests\integration\controller;

use NinjaWars\core\data\Npc;
use NinjaWars\core\data\Player;
use NinjaWars\core\control\NpcController;
use NinjaWars\core\extensions\SessionFactory;
use NinjaWars\core\environment\RequestWrapper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use \TestAccountCreateAndDestroy;
use \PHPUnit_Framework_TestCase;

/**
 * BE AWARE THAT NPCS THAT ARE STRONG ENOUGH (doesn't take much) WILL KILL YOU
 * THIS CAN TRIVIALLY SKEW TESTS, SO GAME THE SYSTEM AGAINST THAT IN YOUR TESTS
 *
 *
 */
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
        RequestWrapper::inject(Request::create('/npc/attack/peasant'));
        $response = $this->controller->attack();
        $this->assertNotEmpty($response);
        $reflection = new \ReflectionProperty(get_class($response), 'data');
        $reflection->setAccessible(true);
        $response_data = $reflection->getValue($response);
        $this->assertEquals('peasant', $response_data['victim']);
    }

    public function testAttackPeasantWithABountableHighLevelCharacter() {
        RequestWrapper::inject(Request::create('/npc/attack/peasant'));
        // Bump the test player's level for bounty purposes.
        $this->char->level = 20;
        $this->char->save();
        $response = $this->controller->attack();
        $this->assertNotEmpty($response);
        $final_char = Player::find($this->char->id());
        $this->assertGreaterThan(0, $final_char->bounty);
    }

    public function testControllerAttackAsIfAgainstAPeasant2() {
        RequestWrapper::inject(Request::create('/npc/attack/peasant2'));
        $response = $this->controller->attack();
        $final_char = Player::find($this->char->id());
        $this->assertNotEmpty($response);
        $reflection = new \ReflectionProperty(get_class($response), 'data');
        $reflection->setAccessible(true);
        $response_data = $reflection->getValue($response);
        $this->assertEquals('peasant2', $response_data['victim']);
        $this->assertGreaterThan(0, $final_char->health);
    }

    public function testControllerAttackAsIfAgainstAMerchant() {
        RequestWrapper::inject(Request::create('/npc/attack/merchant'));
        $response = $this->controller->attack();
        $reflection = new \ReflectionProperty(get_class($response), 'data');
        $reflection->setAccessible(true);
        $response_data = $reflection->getValue($response);
        $this->assertEquals('merchant', $response_data['victim']);
    }

    public function testControllerAttackAsIfAgainstAMerchant2() {
        $this->markTestSkipped('Merchants are unreliable to test for now.');
        RequestWrapper::inject(Request::create('/npc/attack/merchant2'));
        $this->char->strength = 9999;
        $this->char->health = 9999;
        $init_gold = $this->char->gold;
        $npco = new Npc('merchant2');
        $response = $this->controller->attack();
        $final_char = Player::find($this->char->id());
        $this->assertNotEmpty($response);
        $reflection = new \ReflectionProperty(get_class($response), 'data');
        $reflection->setAccessible(true);
        $response_data = $reflection->getValue($response);
        $this->assertEquals('merchant2', $response_data['victim']);
        $this->assertGreaterThan(0, $npco->minGold());
        $this->assertGreaterThan($init_gold, $final_char->gold);
    }

    public function testControllerAttackAsIfAgainstAGuard() {
        RequestWrapper::inject(Request::create('/npc/attack/guard'));
        $response = $this->controller->attack();
        $this->assertNotEmpty($response);
        $reflection = new \ReflectionProperty(get_class($response), 'data');
        $reflection->setAccessible(true);
        $response_data = $reflection->getValue($response);
        $this->assertEquals('guard', $response_data['victim']);
    }

    public function testControllerAttackAsIfAgainstAGuard2() {
        RequestWrapper::inject(Request::create('/npc/attack/guard2'));
        $response = $this->controller->attack();
        $this->assertNotEmpty($response);
        $reflection = new \ReflectionProperty(get_class($response), 'data');
        $reflection->setAccessible(true);
        $response_data = $reflection->getValue($response);
        $this->assertEquals('guard2', $response_data['victim']);
    }

    public function testControllerAttackAsIfAgainstAThief() {
        RequestWrapper::inject(Request::create('/npc/attack/thief'));
        $response = $this->controller->attack();
        $reflection = new \ReflectionProperty(get_class($response), 'data');
        $reflection->setAccessible(true);
        $response_data = $reflection->getValue($response);
        $this->assertEquals('thief', $response_data['victim']);
    }

    public function testControllerAttackAgainstSamurai() {
        $this->char->kills = 40;
        $this->char->level = 5;
        $this->char->strength = 25;
        $this->char->addStatus(STEALTH);
        $this->char->save();

        RequestWrapper::inject(Request::create('/npc/attack/samurai'));
        $response = $this->controller->attack();
        $reflection = new \ReflectionProperty(get_class($response), 'data');
        $reflection->setAccessible(true);
        $response_data = $reflection->getValue($response);
        $this->assertEquals('samurai', $response_data['victim']);
    }

    public function testControllerFailedAttackAgainstSamurai() {
        RequestWrapper::inject(Request::create('/npc/attack/samurai'));
        $response = $this->controller->attack();
        $reflection = new \ReflectionProperty(get_class($response), 'data');
        $reflection->setAccessible(true);
        $response_data = $reflection->getValue($response);
        $this->assertEquals('samurai', $response_data['victim']);
    }

    public function testRandomEncounter() {
        $this->controller = new NpcController([
            'randomness' => function(){ return 1; }
        ]);

        RequestWrapper::inject(Request::create('/npc/attack/peasant'));
        $response = $this->controller->attack();
        $this->assertNotEmpty($response);
    }
}
