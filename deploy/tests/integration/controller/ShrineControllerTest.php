<?php
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use NinjaWars\core\environment\RequestWrapper;
use NinjaWars\core\control\ShrineController;
use NinjaWars\core\data\Player;
use NinjaWars\core\data\Skill;
use NinjaWars\core\extensions\SessionFactory;

class ShrineControllerTest extends NWTest {
    private $char;

	function setUp() {
        parent::setUp();
        $this->char = TestAccountCreateAndDestroy::char();
        $request = new Request([], []);
        RequestWrapper::inject($request);
		SessionFactory::init(new MockArraySessionStorage());
        $sess = SessionFactory::getSession();
        $sess->set('player_id', $this->char->id());
	}

	function tearDown() {
        TestAccountCreateAndDestroy::destroy();
        RequestWrapper::inject(new Request([]));
        $session = SessionFactory::getSession();
        $session->invalidate();
        parent::tearDown();
    }

    public function testShrineControllerCanBeInstantiatedWithoutError() {
        $cont = new ShrineController();
        $this->assertInstanceOf(ShrineController::class, $cont);
    }

    public function testShrineIndexDoesNotError() {
        $cont = new ShrineController();
        $cont_outcome = $cont->index($this->m_dependencies);
        $this->assertNotEmpty($cont_outcome);
    }

    public function testShrineIndexFullHealthNotice() {
        $player = new Player();
        $player->level = 1;
        $player->health = $player->getMaxHealth();

        $this->m_dependencies['current_player'] = function($c) use ($player) {
            return $player;
        };

        $cont = new ShrineController();
        $response = $cont->index($this->m_dependencies);

        $reflection = new \ReflectionProperty(get_class($response), 'data');
        $reflection->setAccessible(true);
        $response_data = $reflection->getValue($response);

        $this->assertContains('reminder-full-hp', $response_data['pageParts']);
    }

    public function testShrineIndexPoisonedNotice() {
        $player = new Player();
        $player->level = 1;
        $player->health = $player->getMaxHealth();
        $player->addStatus(POISON);

        $this->m_dependencies['current_player'] = function($c) use ($player) {
            return $player;
        };

        $cont = new ShrineController();
        $response = $cont->index($this->m_dependencies);

        $reflection = new \ReflectionProperty(get_class($response), 'data');
        $reflection->setAccessible(true);
        $response_data = $reflection->getValue($response);

        $this->assertContains('form-cure', $response_data['pageParts']);
    }

    public function testHealAndResurrectOfDeadPlayer(){
        $this->char->death();
        $this->char->save();

        $cont = new ShrineController();
        $response = $cont->healAndResurrect($this->m_dependencies);

        $reflection = new \ReflectionProperty(get_class($response), 'data');
        $reflection->setAccessible(true);
        $response_data = $reflection->getValue($response);
        $this->assertContains('result-resurrect', $response_data['pageParts']);

        $final_char = Player::find($this->char->id());
        $this->assertEquals($final_char->getMaxHealth(), $final_char->health);
    }

    /**
     * Test partial heal of player heals them some
     */
    public function testShrinePartialHeal() {
        $request = new Request(['heal_points' => 10]);
        RequestWrapper::inject($request);
        $this->char->harm(floor($this->char->health/2)); // Have to be wounded first.
        $this->char->setClass('viper'); // Default dragon class has chi skill
        $this->char->save();

        $initial_health = $this->char->health;
        $this->assertGreaterThan(0, $initial_health);

        $cont = new ShrineController();
        $response = $cont->heal();
        $reflection = new \ReflectionProperty(get_class($response), 'data');
        $reflection->setAccessible(true);
        $response_data = $reflection->getValue($response);
        $this->assertTrue(in_array('result-heal', $response_data['pageParts']));
        $final_char = Player::find($this->char->id());
        $this->assertEquals($initial_health+10, $final_char->health);
    }

    /**
     * Test max heal of player
     */
    public function testShrineMaxHeal(){
        $request = new Request(['heal_points'=>'max'], []);
        RequestWrapper::inject($request);
        $this->char->harm((int)floor($this->char->health/2)); // Have to be wounded first.
        $initial_health = $this->char->health;
        $this->char->gold = 999999;  // Ensure enough gold to heal.
        $initial_gold = $this->char->gold;
        $this->char->setClass('viper'); // ensure no chi
        $this->char->save();

        $cont = new ShrineController();
        $response = $cont->heal();
        $reflection = new \ReflectionProperty(get_class($response), 'data');
        $reflection->setAccessible(true);
        $response_data = $reflection->getValue($response);
        $this->assertTrue(in_array('result-heal', $response_data['pageParts']));
        $final_char = Player::find($this->char->id());
        $this->assertEquals(min($initial_health+$initial_gold, $final_char->getMaxHealth()), $final_char->health);
        $this->assertEquals(Player::maxHealthByLevel($final_char->level), $final_char->health);

    }

    public function testPartialHealWithZeroGoldGivesErrorInPageParts(){
        $request = new Request(['heal_points'=>999], []);
        RequestWrapper::inject($request);
        $this->char->harm((int)floor($this->char->health/2)); // Have to be wounded first.
        $this->char->setGold(0);
        $initial_health = $this->char->health;
        $this->assertGreaterThan(0, $initial_health);
        $this->char->save();
        $this->char->setClass('viper'); // Default dragon class has chi skill

        $cont = new ShrineController();
        $response = $cont->heal();
        $final_char = Player::find($this->char->id());
        $reflection = new \ReflectionProperty(get_class($response), 'data');
        $reflection->setAccessible(true);
        $response_data = $reflection->getValue($response);
        $this->assertNotEmpty($response_data['error']);
        $this->assertEquals($initial_health, $final_char->health);
    }

    public function testResurrectOfPlayerByShrine(){
        $this->char->death();
        $this->char->save();

        $cont = new ShrineController();
        $response = $cont->resurrect();
        $final_char = Player::find($this->char->id());
        $reflection = new \ReflectionProperty(get_class($response), 'data');
        $reflection->setAccessible(true);
        $response_data = $reflection->getValue($response);
        $this->assertTrue(in_array('result-resurrect', $response_data['pageParts']));
        $this->assertGreaterThan(floor(Player::maxHealthByLevel($this->char->level)/2), $final_char->health);
    }

    public function testAntidoteUnpoisoningOfPoisonedCharacter(){
        $this->char->addStatus(POISON);
        $this->char->save();
        $this->assertTrue($this->char->hasStatus(POISON));

        $cont = new ShrineController();
        $cont->cure();
        $final_char = Player::find($this->char->id());
        $this->assertFalse($final_char->hasStatus(POISON));
    }

    public function testFreeResurrectWithChi() {
        $this->char->death();
        $this->char->setClass('dragon'); // dragon class has chi skill
        $this->char->save();

        $cont = new ShrineController();
        $response = $cont->resurrect();
        $final_char = Player::find($this->char->id());
        $reflection = new \ReflectionProperty(get_class($response), 'data');
        $reflection->setAccessible(true);
        $response_data = $reflection->getValue($response);
        $this->assertTrue(in_array('result-resurrect', $response_data['pageParts']));
        $this->assertGreaterThan(floor(Player::maxHealthByLevel($this->char->level)/2), $final_char->health);
    }

    public function testKillCostResurrectWithChi() {
        $this->char->death();
        $this->char->setClass('dragon'); // dragon class has chi skill
        $this->char->level = ShrineController::FREE_RES_LEVEL_LIMIT;
        $this->char->kills = ShrineController::FREE_RES_KILL_LIMIT;
        $this->char->save();

        $cont = new ShrineController();
        $response = $cont->resurrect();
        $final_char = Player::find($this->char->id());
        $reflection = new \ReflectionProperty(get_class($response), 'data');
        $reflection->setAccessible(true);
        $response_data = $reflection->getValue($response);
        $this->assertTrue(in_array('result-resurrect', $response_data['pageParts']));
        $this->assertGreaterThan($this->char->getMaxHealth()/(3), $final_char->health);
    }

    public function testKillCostResurrectWithStealth() {
        $this->char->death();
        $this->char->setClass('viper'); // viper class has stealth
        $this->char->level = ShrineController::FREE_RES_LEVEL_LIMIT;
        $this->char->kills = ShrineController::FREE_RES_KILL_LIMIT;
        $this->char->save();

        $cont = new ShrineController();
        $response = $cont->resurrect();
        $final_char = Player::find($this->char->id());

        $reflection = new \ReflectionProperty(get_class($response), 'data');
        $reflection->setAccessible(true);
        $response_data = $reflection->getValue($response);
        $this->assertTrue(in_array('result-resurrect', $response_data['pageParts']));
        $this->assertTrue($final_char->hasStatus(STEALTH));
    }

    public function testTurnCostResurrectWithChi() {
        $turns = 50;
        $this->char->death();
        $this->char->setClass('dragon'); // dragon class has chi skill
        $this->char->level = ShrineController::FREE_RES_LEVEL_LIMIT;
        $this->char->turns = $turns;
        $this->char->save();
        $skillList = new Skill();
        $this->assertTrue($skillList->hasSkill('chi', $this->char));

        $cont = new ShrineController();
        $response = $cont->resurrect();
        $final_char = Player::find($this->char->id());
        $reflection = new \ReflectionProperty(get_class($response), 'data');
        $reflection->setAccessible(true);
        $response_data = $reflection->getValue($response);
        $this->assertTrue(in_array('result-resurrect', $response_data['pageParts']));
        $this->assertGreaterThan($this->char->getMaxHealth()/(1.5), $final_char->health);
        $this->assertLessThan($turns, $final_char->turns);
    }

    public function testResurrectOnEmpty() {
        $this->char->death();
        $this->char->level = ShrineController::FREE_RES_LEVEL_LIMIT;
        $this->char->turns = 0;
        $this->char->kills = 0;
        $this->char->save();

        $cont = new ShrineController();
        $response = $cont->resurrect();
        $reflection = new \ReflectionProperty(get_class($response), 'data');
        $reflection->setAccessible(true);
        $response_data = $reflection->getValue($response);
        $this->assertFalse(in_array('result-resurrect', $response_data['pageParts']));
    }

    public function testResurrectWhileAlive() {
        $this->char->level = ShrineController::FREE_RES_LEVEL_LIMIT;
        $this->char->turns = 0;
        $this->char->kills = 0;
        $this->char->save();

        $cont = new ShrineController();
        $response = $cont->resurrect();
        $reflection = new \ReflectionProperty(get_class($response), 'data');
        $reflection->setAccessible(true);
        $response_data = $reflection->getValue($response);
        $this->assertFalse(in_array('result-resurrect', $response_data['pageParts']));
    }
}
