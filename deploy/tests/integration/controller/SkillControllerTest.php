<?php
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\HttpFoundation\RedirectResponse;
use NinjaWars\core\environment\RequestWrapper;
use NinjaWars\core\control\SkillController;
use NinjaWars\core\extensions\SessionFactory;
use NinjaWars\core\data\Skill;
use \TestAccountCreateAndDestroy as TestAccountCreateAndDestroy;
use \Player as Player;

class SkillControllerTest extends \PHPUnit_Framework_TestCase {
	public function setUp() {
        $this->char = Player::find(TestAccountCreateAndDestroy::char_id());
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
        $this->char->set_turns(300);
        $this->char->vo->level = 20;
        $this->assertNull($error);
        $initial_health = $this->char2->health();
        $name = $this->char2->name();
        $this->assertNotEmpty($name);
        $this->assertNotEmpty(url($name));
        $skillList = new Skill();
        $this->assertTrue($skillList->hasSkill('Fire Bolt', $this->char));
        $request = Request::create('/skill/use/Fire%20Bolt/'.url($name).'/');
        RequestWrapper::inject($request);
        $skill = new SkillController();
        $skill_outcome = $skill->go();
        $final_defender = Player::find($this->char2->id());
        $this->assertNotInstanceOf('Symfony\Component\HttpFoundation\RedirectResponse', $skill_outcome, 
                'A redirect was the outcome for the url: '
                .($skill_outcome instanceof RedirectResponse? $skill_outcome->getTargetUrl() : ''));
        $this->assertNull($skill_outcome['parts']['error']);
        $this->assertLessThan($initial_health, $final_defender->health());
    }

    public function testWhenIFireBoltACharacterAndKillIShouldReceiveBounty(){
        $error = $this->char->setClass('tiger');
        $bounty = 300;
        $self_gold = $this->char->gold();
        $this->char->set_turns(300);
        $this->char->vo->level = 2;
        $this->assertNull($error);
        $this->char2->set_health(1);
        $this->char2->vo->level = 200; // To ensure higher level.
        $this->char2->set_gold(0); // No gold
        $this->char2->set_bounty($bounty); // Only bounty
        $this->char2->save();
        $this->char->save();
        $initial_health = $this->char2->health();
        $name = $this->char2->name();
        $this->assertNotEmpty($name);
        $this->assertNotEmpty(url($name));
        $skillList = new Skill();
        $this->assertTrue($skillList->hasSkill('Fire Bolt', $this->char));
        $request = Request::create('/skill/use/Fire%20Bolt/'.url($name).'/');
        RequestWrapper::inject($request);
        $skill = new SkillController();
        $skill_outcome = $skill->go();
        $final_defender = Player::find($this->char2->id());
        $final_attacker = Player::find($this->char->id());
        $this->assertNotInstanceOf('Symfony\Component\HttpFoundation\RedirectResponse', $skill_outcome, 
                'A redirect was the outcome for the url: '
                .($skill_outcome instanceof RedirectResponse? $skill_outcome->getTargetUrl() : ''));
        $this->assertNull($skill_outcome['parts']['error']);
        $this->assertEquals(0, $final_defender->health());
        $this->assertEquals(0, $final_defender->bounty());
        $this->assertEquals($self_gold+$bounty, $final_attacker->gold());
    }

    public function testIShouldGetBountyOnMyHeadWhenIFireBoltKillALowLevel(){
        $this->char->setClass('tiger');
        $self_gold = $this->char->gold();
        $this->char->set_turns(300);
        $this->char->vo->level = 200;
        $this->char->set_bounty(0);
        $this->char2->set_health(1);
        $this->char2->vo->level = 2; // Ensure a lower level
        $this->char2->set_gold(0); // No gold to get from target
        $this->char2->save();
        $this->char->save();
        $initial_bounty = $this->char->bounty();
        $initial_health = $this->char2->health();
        $name = $this->char2->name();
        $this->assertNotEmpty($name);
        $this->assertNotEmpty(url($name));
        $skillList = new Skill();
        $this->assertTrue($skillList->hasSkill('Fire Bolt', $this->char));
        $request = Request::create('/skill/use/Fire%20Bolt/'.url($name).'/');
        RequestWrapper::inject($request);
        $skill = new SkillController();
        $skill_outcome = $skill->go();
        $final_defender = Player::find($this->char2->id());
        $final_attacker = Player::find($this->char->id());
        $this->assertNotInstanceOf('Symfony\Component\HttpFoundation\RedirectResponse', $skill_outcome, 
                'A redirect was the outcome for the url: '
                .($skill_outcome instanceof RedirectResponse? $skill_outcome->getTargetUrl() : ''));
        $this->assertNull($skill_outcome['parts']['error']);
        $this->assertEquals(0, $final_defender->health());
        $this->assertGreaterThan($initial_bounty, $final_attacker->bounty());
    }

    public function testUseUnstealthOnSelf(){
        $this->char->setClass('viper');
        $this->char->set_turns(300);
        $this->char->vo->level = 20;
        $this->char->save();

        $request = Request::create('/skill/self_use/Unstealth/');
        RequestWrapper::inject($request);
        $controller = new SkillController();
        $controller_outcome = $controller->selfUse();

        $this->assertNotInstanceOf('Symfony\Component\HttpFoundation\RedirectResponse', $controller_outcome, 
                'A redirect was the outcome for the url: '
                .($controller_outcome instanceof RedirectResponse? $controller_outcome->getTargetUrl() : ''));
        $this->assertEquals('Unstealth', $controller_outcome['parts']['act']);
    }


    public function testUsePoisonTouchOnAnotherChar(){
        $error = $this->char->setClass('viper');
        $this->char->set_turns(300);
        $this->char->vo->level = 20;
        $this->assertNull($error);
        $initial_health = $this->char2->health();
        $name = $this->char2->name();
        $this->assertNotEmpty($name);
        $this->assertNotEmpty(url($name));
        $skillList = new Skill();
        $this->assertTrue($skillList->hasSkill('Poison Touch', $this->char));
        $request = Request::create('/skill/use/Poison%20Touch/'.url($name).'/');
        RequestWrapper::inject($request);
        $skill = new SkillController();
        $skill_outcome = $skill->go();

        $final_defender = Player::find($this->char2->id());
        $this->assertNotInstanceOf('Symfony\Component\HttpFoundation\RedirectResponse', $skill_outcome, 
                'A redirect was the outcome for the url: '
                .($skill_outcome instanceof RedirectResponse? $skill_outcome->getTargetUrl() : ''));
        $this->assertNull($skill_outcome['parts']['error']);
        $this->assertEquals('Poison Touch', $skill_outcome['parts']['act']);
        $this->assertLessThan($initial_health, $final_defender->health());
    }

    public function testUseSightOnAnotherChar(){
        $error = $this->char->setClass('dragon');
        $this->char->set_turns(300);
        $this->char->vo->level = 20;
        $this->assertNull($error);
        $this->char->save();
        $name = $this->char2->name();
        $this->assertNotEmpty($name);
        $this->assertNotEmpty(url($name));
        $skillList = new Skill();
        $this->assertTrue($skillList->hasSkill('Sight', $this->char));
        $request = Request::create('/skill/use/Sight/'.url($name).'/');
        RequestWrapper::inject($request);
        $skill = new SkillController();
        $skill_outcome = $skill->go();

        $this->assertNotInstanceOf('Symfony\Component\HttpFoundation\RedirectResponse', $skill_outcome, 
                'An error redirect was sent back for the url: '
                .($skill_outcome instanceof RedirectResponse? $skill_outcome->getTargetUrl() : ''));
        $this->assertNull($skill_outcome['parts']['error']);
        $this->assertEquals('Sight', $skill_outcome['parts']['act']);
    }

    // TODO: test that self_use of things like Steal error or whatever the right behavior should be?
    // TODO: test that use of skills that aren't part of the users skillset error

    public function testUseHealOnSelfAsAHealingCharacter(){
        $this->char->setClass('dragon');
        $this->char->set_turns(300);
        $this->char->vo->level = 20;
        $this->char->set_health(floor($this->char->getMaxHealth()/2));
        $initial_health = $this->char->health();
        $this->assertGreaterThan($initial_health, $this->char->getMaxHealth());
        $this->char->save();

        $request = Request::create('/skill/self_use/Heal/');
        RequestWrapper::inject($request);
        $controller = new SkillController();
        $controller_outcome = $controller->selfUse();

        $this->assertNotInstanceOf('Symfony\Component\HttpFoundation\RedirectResponse', $controller_outcome, 
                'A redirect was the outcome for the url: '
                .($controller_outcome instanceof RedirectResponse? $controller_outcome->getTargetUrl() : ''));
        $this->assertEquals('Heal', $controller_outcome['parts']['act']);
        $final_char = Player::find($this->char->id());
        $this->assertGreaterThan($initial_health, $final_char->health());
    }
}
