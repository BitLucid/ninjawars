<?php
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use NinjaWars\core\environment\RequestWrapper;
use NinjaWars\core\control\SkillController;
use NinjaWars\core\extensions\SessionFactory;
use \Player as Player;

class SkillControllerTest extends PHPUnit_Framework_TestCase {
	public function setUp() {
        $this->char = TestAccountCreateAndDestroy::char();
        $this->char2 = Player::find(TestAccountCreateAndDestroy::char_id_2());
        SessionFactory::init(new MockArraySessionStorage());
        $session = SessionFactory::getSession();
        $session->set('player_id', $this->char->id()); // Mock the login.

        $request = new Request([], []);
        RequestWrapper::inject($request);
	}

	public function tearDown() {
        $this->char = null;
        $this->char2 = null;
        TestAccountCreateAndDestroy::destroy();
        RequestWrapper::inject(new Request([]));
        $session = SessionFactory::getSession();
        $session->invalidate();
    }

    public function testLoggedInSkillsDisplay(){
        $skill = new SkillController();
        $skill_outcome = $skill->index();
        $this->assertNotEmpty($skill_outcome);
        $this->assertEquals('Your Skills', $skill_outcome['title']);
    }

    public function testUseFireboltOnAnotherChar(){
        $passfail = $this->char->setClass('tiger');
        $this->assertNull($passfail);
        $initial_health = $this->char2->health();
        $request = Request::create('/skill/use/Fire%20Bolt/'.url($this->char2->name()).'/');
        RequestWrapper::inject($request);
        $final_defender = Player::find($this->char2->id());
        $skill = new SkillController();
        $skill_outcome = $skill->go();
        $this->assertLessThan($initial_health, $final_defender->health());
    }

    public function testUseHealOnSelfAsDragon(){
        $passfail = $this->char->setClass('dragon');
        $this->assertNull($passfail);
        $this->char->save();

        $initial_health = $this->char2->health();

        $request = Request::create('/skill/self_use/Heal/');
        RequestWrapper::inject($request);
        $final_defender = Player::find($this->char2->id());
        $skill = new SkillController();
        $skill_outcome = $skill->selfUse();
        $this->assertLessThan($initial_health, $final_defender->health());
    }
}
