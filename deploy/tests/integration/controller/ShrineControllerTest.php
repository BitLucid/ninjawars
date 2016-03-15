<?php
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use NinjaWars\core\environment\RequestWrapper;
use NinjaWars\core\control\ShrineController;
use NinjaWars\core\extensions\SessionFactory;

class ShrineControllerTest extends PHPUnit_Framework_TestCase {
    private $char;

	function setUp() {
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
    }

    public function testShrineControllerCanBeInstantiatedWithoutError() {
        $cont = new ShrineController();
        $this->assertInstanceOf('NinjaWars\core\control\ShrineController', $cont);
    }

    public function testShrineIndexDoesNotError() {
        $cont = new ShrineController();
        $cont_outcome = $cont->index();
        $this->assertNotEmpty($cont_outcome);
    }

    public function testHealAndResurrectOfDeadPlayer(){
        $this->char->death();
        $this->char->save();

        $cont = new ShrineController();
        $result = $cont->healAndResurrect();
        $final_char = Player::find($this->char->id());
        $this->assertTrue(in_array('result-resurrect', $result['parts']['pageParts']));
        $this->assertEquals($final_char->max_health(), $final_char->health());
    }

    /**
     * Test partial heal of player heals them some
     */
    public function testShrinePartialHeal(){
        $request = new Request(['heal_points'=>10], []);
        RequestWrapper::inject($request);
        $this->char->harm(30); // Have to be wounded first.
        $initial_health = $this->char->health();
        $this->assertGreaterThan(0, $initial_health);
        $this->char->save();
        $this->char->setClass('viper'); // Default dragon class has chi skill

        $cont = new ShrineController();
        $result = $cont->heal();
        $this->assertTrue(in_array('result-heal', $result['parts']['pageParts']));
        $final_char = Player::find($this->char->id());
        $this->assertEquals($initial_health+10, $final_char->health());
    }

    /**
     * Test max heal of player
     */
    public function testShrineMaxHeal(){
        $request = new Request(['heal_points'=>'max'], []);
        RequestWrapper::inject($request);
        $this->char->harm(30); // Have to be wounded first.
        $initial_health = $this->char->health();
        $initial_gold = $this->char->gold;
        $this->char->setClass('viper'); // ensure no chi
        $this->char->save();

        $cont = new ShrineController();
        $result = $cont->heal();
        $this->assertTrue(in_array('result-heal', $result['parts']['pageParts']));
        $final_char = Player::find($this->char->id());
        $this->assertEquals(min($initial_health+$initial_gold, $final_char->getMaxHealth()), $final_char->health());
    }

    public function testPartialHealWithZeroGoldGivesErrorInPageParts(){
        $request = new Request(['heal_points'=>999], []);
        RequestWrapper::inject($request);
        $this->char->harm(30); // Have to be wounded first.
        $this->char->set_gold(0);
        $initial_health = $this->char->health();
        $this->assertGreaterThan(0, $initial_health);
        $this->char->save();
        $this->char->setClass('viper'); // Default dragon class has chi skill

        $cont = new ShrineController();
        $result = $cont->heal();
        $final_char = Player::find($this->char->id());
        $this->assertNotEmpty($result['parts']['error']);
        $this->assertEquals($initial_health, $final_char->health());
    }


    public function testResurrectOfPlayerByShrine(){
        $this->char->death();
        $this->char->save();

        $cont = new ShrineController();
        $result = $cont->resurrect();
        $final_char = Player::find($this->char->id());
        $this->assertTrue(in_array('result-resurrect', $result['parts']['pageParts']));
        $this->assertGreaterThan(floor(Player::maxHealthByLevel($this->char->level)/2), $final_char->health());
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
        $result = $cont->resurrect();
        $final_char = Player::find($this->char->id());
        $this->assertTrue(in_array('result-resurrect', $result['parts']['pageParts']));
        $this->assertGreaterThan(floor(Player::maxHealthByLevel($this->char->level)/2), $final_char->health());
    }

    public function testKillCostResurrectWithChi() {
        $this->char->death();
        $this->char->setClass('dragon'); // dragon class has chi skill
        $this->char->vo->level = ShrineController::FREE_RES_LEVEL_LIMIT;
        $this->char->vo->kills = ShrineController::FREE_RES_KILL_LIMIT;
        $this->char->save();

        $cont = new ShrineController();
        $result = $cont->resurrect();
        $final_char = Player::find($this->char->id());
        $this->assertTrue(in_array('result-resurrect', $result['parts']['pageParts']));
        $this->assertGreaterThan($this->char->level*10, $final_char->health());
    }

    public function testKillCostResurrectWithStealth() {
        $this->char->death();
        $this->char->setClass('viper'); // viper class has stealth
        $this->char->vo->level = ShrineController::FREE_RES_LEVEL_LIMIT;
        $this->char->vo->kills = ShrineController::FREE_RES_KILL_LIMIT;
        $this->char->save();

        $cont = new ShrineController();
        $result = $cont->resurrect();
        $final_char = Player::find($this->char->id());

        $this->assertTrue(in_array('result-resurrect', $result['parts']['pageParts']));
        $this->assertTrue($final_char->hasStatus(STEALTH));
    }

    public function testTurnCostResurrectWithChi() {
        $turns = 50;
        $this->char->death();
        $this->char->setClass('dragon'); // dragon class has chi skill
        $this->char->vo->level = ShrineController::FREE_RES_LEVEL_LIMIT;
        $this->char->vo->turns = $turns;
        $this->char->save();

        $cont = new ShrineController();
        $result = $cont->resurrect();
        $final_char = Player::find($this->char->id());
        $this->assertTrue(in_array('result-resurrect', $result['parts']['pageParts']));
        $this->assertLessThan($final_char->health(), $this->char->level*10);
        $this->assertLessThan($turns, $final_char->turns);
    }

    public function testResurrectOnEmpty() {
        $this->char->death();
        $this->char->vo->level = ShrineController::FREE_RES_LEVEL_LIMIT;
        $this->char->vo->turns = 0;
        $this->char->vo->kills = 0;
        $this->char->save();

        $cont = new ShrineController();
        $result = $cont->resurrect();
        $this->assertFalse(in_array('result-resurrect', $result['parts']['pageParts']));
    }

    public function testResurrectWhileAlive() {
        $this->char->vo->level = ShrineController::FREE_RES_LEVEL_LIMIT;
        $this->char->vo->turns = 0;
        $this->char->vo->kills = 0;
        $this->char->save();

        $cont = new ShrineController();
        $result = $cont->resurrect();
        $this->assertFalse(in_array('result-resurrect', $result['parts']['pageParts']));
    }
}
