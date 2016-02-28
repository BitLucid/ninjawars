<?php
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use NinjaWars\core\environment\RequestWrapper;
use NinjaWars\core\control\ShrineController;
use NinjaWars\core\extensions\SessionFactory;

class ShrineControllerTest extends PHPUnit_Framework_TestCase {
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

    /**
     * Test partial heal of player heals them some
     */
    public function testShrinePartialHeal(){
        $request = new Request(['heal_points'=>10], []);
        RequestWrapper::inject($request);
        $this->char->harm(30); // Have to be wounded first.
        $initial_health = $this->char->health();
        $this->char->save();
        $this->char->setClass('viper'); // Default dragon class has chi skill

        $cont = new ShrineController();
        $result = $cont->heal();
        $this->assertTrue(in_array('result-heal', $result['parts']['pageParts']));
        $final_char = Player::find($this->char->id());
        $this->assertEquals($initial_health+10, $final_char->health());
    }

    // Test shrine can heal and resurrect dead player
    // Test shrine heal_and_resurrect heals hurt player

    public function testPartialHealWithZeroGoldGivesErrorInPageParts(){
        $request = new Request(['heal_points'=>999], []);
        RequestWrapper::inject($request);
        $this->char->harm(30); // Have to be wounded first.
        $this->char->set_gold(0);
        $initial_health = $this->char->health();
        $this->char->save();
        $this->char->setClass('viper'); // Default dragon class has chi skill

        $cont = new ShrineController();
        $result = $cont->heal();
        $final_char = Player::find($this->char->id());
        $this->assertNotEmpty($result['parts']['error']);
        $this->assertEquals($initial_health, $final_char->health());
    }
}
