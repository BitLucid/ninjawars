<?php
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\HttpFoundation\RedirectResponse as RedirectResponse;
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
        $error = $this->char->setClass('tiger');
        $this->assertNull($error);
        $initial_health = $this->char2->health();
        $name = $this->char2->name();
        $this->assertNotEmpty($name);
        $this->assertNotEmpty(url($name));
        $skillList = new Skill();
        $this->assertTrue($skillList->hasSkill('Fire Bolt'));
        $request = Request::create('/skill/use/Fire%20Bolt/'.url($name).'/');
        RequestWrapper::inject($request);
        $skill = new SkillController();
        $skill_outcome = $skill->go();
        $final_defender = Player::find($this->char2->id());
        $this->assertNotInstanceOf('Symfony\Component\HttpFoundation\RedirectResponse', $skill_outcome, 
                'A redirect was requested for the url: '
                .($skill_outcome instanceof RedirectResponse? $skill_outcome->getTargetUrl() : ''));
        $this->assertNull($skill_outcome['parts']['error']);
        $this->assertLessThan($initial_health, $final_defender->health());
    }

    public function testUseSightOnAnotherChar(){
        $error = $this->char->setClass('dragon');
        $this->assertNull($error);
        $this->char->save();
        $name = $this->char2->name();
        $this->assertNotEmpty($name);
        $this->assertNotEmpty(url($name));
        $skillList = new Skill();
        $this->assertTrue($skillList->hasSkill('Sight'));
        $request = Request::create('/skill/use/Sight/'.url($name).'/');
        RequestWrapper::inject($request);
        $skill = new SkillController();
        $skill_outcome = $skill->go();
        $this->assertNotInstanceOf('Symfony\Component\HttpFoundation\RedirectResponse', $skill_outcome, 
                'A redirect was requested for the url: '
                .($skill_outcome instanceof RedirectResponse? $skill_outcome->getTargetUrl() : ''));
        $this->assertNull($skill_outcome['parts']['error']);
    }


    public function testUseHealOnSelfAsDragon(){
        $error = $this->char->setClass('dragon');
        $this->assertNull($error);
        $this->char->harm(50); // Make some healable damage
        $this->char->save();

        $initial_health = $this->char->health();

        $request = Request::create('/skill/self_use/Heal/');
        RequestWrapper::inject($request);
        $skill = new SkillController();
        $skill_outcome = $skill->selfUse();
        $final_pc = Player::find($this->char->id());
        $this->assertNotInstanceOf('Symfony\Component\HttpFoundation\RedirectResponse', $skill_outcome);
        $this->assertLessThan($initial_health, $final_pc->health());
    }
}
