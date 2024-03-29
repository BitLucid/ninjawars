<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\HttpFoundation\RedirectResponse;
use NinjaWars\core\environment\RequestWrapper;
use NinjaWars\core\control\SkillController;
use NinjaWars\core\extensions\SessionFactory;
use NinjaWars\core\data\Skill;
use NinjaWars\core\data\Player;
use TestAccountCreateAndDestroy as TestAccountCreateAndDestroy;

class SkillControllerTest extends NWTest
{
    public function setUp(): void
    {
        parent::setUp();
        $this->char = TestAccountCreateAndDestroy::char();
        $this->char2 = TestAccountCreateAndDestroy::char_2();
        SessionFactory::init(new MockArraySessionStorage());
        $session = SessionFactory::getSession();
        $session->set('player_id', $this->char->id()); // Mock the login.

        $request = new Request([], []);
        RequestWrapper::inject($request);
    }

    public function tearDown(): void
    {
        $this->char = null;
        $this->char2 = null;
        TestAccountCreateAndDestroy::destroy();
        RequestWrapper::inject(new Request([]));
        $session = SessionFactory::getSession();
        $session->invalidate();
        parent::tearDown();
    }

    public function testLoggedInSkillsDisplay()
    {
        $skill = new SkillController();
        $skill->update_timer = false;
        $response = $skill->index($this->m_dependencies);
        $this->assertNotEmpty($response);
        $reflection = new \ReflectionProperty(get_class($response), 'title');
        $reflection->setAccessible(true);
        $response_title = $reflection->getValue($response);
        $this->assertEquals('Your Skills', $response_title);
    }

    public function testUseFireboltOnAnotherChar()
    {
        $this->char->setTurns(300);
        $this->char->level = 20;
        $initial_health = $this->char2->health;
        $error = $this->char->setClass('tiger');
        $this->assertNull($error);
        $this->char->save();
        $name = $this->char2->name();
        $this->assertNotEmpty($name);
        $this->assertNotEmpty(rawurlencode($name));
        $skillList = new Skill();
        $this->assertTrue($skillList->hasSkill('Fire Bolt', $this->char));
        $request = Request::create('/skill/use/Fire%20Bolt/'.rawurlencode($name).'/');
        RequestWrapper::inject($request);
        $skill_use = new SkillController();
        $skill_use->update_timer = false;
        $response = $skill_use->useSkill($this->m_dependencies);
        //$this->assertEmpty($response->data->attack_error, 'Attack errored, was: ['.$response->data->attack_error);
        $final_defender = Player::find($this->char2->id());
        $this->assertNotInstanceOf(
            RedirectResponse::class,
            $response,
            'A redirect was the outcome for the url: '
            .($response instanceof RedirectResponse ? $response->getTargetUrl() : '')
        );
        $reflection = new \ReflectionProperty(get_class($response), 'data');
        $reflection->setAccessible(true);
        $response_data = $reflection->getValue($response);
        $this->assertNull($response_data['error']);
        $this->assertLessThan($initial_health, $final_defender->health);
    }

    public function testUseFireboltOnAnotherCharDecreasesTurns()
    {
        $this->char->setTurns(300);
        $this->char->level = 20;
        $initial_health = $this->char2->health;
        $error = $this->char->setClass('tiger');
        $this->assertNull($error);
        $this->char->save();
        $name = $this->char2->name();
        $this->assertNotEmpty($name);
        $this->assertNotEmpty(rawurlencode($name));
        $skillList = new Skill();
        $this->assertTrue($skillList->hasSkill('Fire Bolt', $this->char));
        $request = Request::create('/skill/use/Fire%20Bolt/'.rawurlencode($name).'/');
        RequestWrapper::inject($request);
        $skill = new SkillController();
        $skill->update_timer = false;
        $response = $skill->useSkill($this->m_dependencies);
        $final_defender = Player::find($this->char2->id());
        $final_attacker = Player::find($this->char->id());
        $this->assertNotInstanceOf(
            RedirectResponse::class,
            $response,
            'A redirect was the outcome for the url: '
            .($response instanceof RedirectResponse ? $response->getTargetUrl() : '')
        );
        $reflection = new \ReflectionProperty(get_class($response), 'data');
        $reflection->setAccessible(true);
        $response_data = $reflection->getValue($response);
        $this->assertNull($response_data['error']);
        $this->assertEquals(298, $final_attacker->turns);
    }

    public function testWhenIFireBoltACharacterAndKillIShouldReceiveBounty()
    {
        $error = $this->char->setClass('tiger');
        $bounty = 300;
        $self_gold = $this->char->gold;
        $this->char->setTurns(300);
        $this->char->level = 2;
        $this->assertNull($error);
        $this->char2->setHealth(1);
        $this->char2->level = 200; // To ensure higher level.
        $this->char2->setGold(0); // No gold
        $this->char2->setBounty($bounty); // Only bounty
        $this->char2->save();
        $this->char->save();
        $name = $this->char2->name();
        $this->assertNotEmpty($name);
        $this->assertNotEmpty(rawurlencode($name));
        $skillList = new Skill();
        $this->assertTrue($skillList->hasSkill('Fire Bolt', $this->char));
        $request = Request::create('/skill/use/Fire%20Bolt/'.rawurlencode($name).'/');
        RequestWrapper::inject($request);
        $skill = new SkillController();
        $skill->update_timer = false;
        $response = $skill->useSkill($this->m_dependencies);
        $final_defender = Player::find($this->char2->id());
        $final_attacker = Player::find($this->char->id());
        $this->assertNotInstanceOf(
            RedirectResponse::class,
            $response,
            'A redirect was the outcome for the url: '
            .($response instanceof RedirectResponse ? $response->getTargetUrl() : '')
        );
        $reflection = new \ReflectionProperty(get_class($response), 'data');
        $reflection->setAccessible(true);
        $response_data = $reflection->getValue($response);
        $this->assertNull($response_data['error']);
        $this->assertEquals(0, $final_defender->health, "Health not 0");
        $this->assertEquals(0, $final_defender->bounty, "Bounty not 0");
        $this->assertEquals($self_gold + $bounty, $final_attacker->gold, "Gold not updated");
    }

    public function testIShouldGetBountyOnMyHeadWhenIFireBoltKillALowLevel()
    {
        $this->char->setClass('tiger');
        $this->char->setTurns(300);
        $this->char->level = 200;
        $this->char->setBounty(0);
        $this->char2->setHealth(1);
        $this->char2->level = 2; // Ensure a lower level
        $this->char2->setGold(0); // No gold to get from target
        $this->char2->save();
        $this->char->save();
        $initial_bounty = $this->char->bounty;
        $name = $this->char2->name();
        $this->assertNotEmpty($name);
        $this->assertNotEmpty(rawurlencode($name));
        $skillList = new Skill();
        $this->assertTrue($skillList->hasSkill('Fire Bolt', $this->char));
        $request = Request::create('/skill/use/Fire%20Bolt/'.rawurlencode($name).'/');
        RequestWrapper::inject($request);
        $skill = new SkillController();
        $skill->update_timer = false;
        $response = $skill->useSkill($this->m_dependencies);
        $final_defender = Player::find($this->char2->id());
        $final_attacker = Player::find($this->char->id());
        $this->assertNotInstanceOf(
            RedirectResponse::class,
            $response,
            'A redirect was the outcome for the url: '
            .($response instanceof RedirectResponse ? $response->getTargetUrl() : '')
        );
        $reflection = new \ReflectionProperty(get_class($response), 'data');
        $reflection->setAccessible(true);
        $response_data = $reflection->getValue($response);
        $this->assertNull($response_data['error']);
        $this->assertEquals(0, $final_defender->health);
        $this->assertGreaterThan($initial_bounty, $final_attacker->bounty);
    }

    public function testUseUnstealthOnSelf()
    {
        $this->char->setClass('viper');
        $this->char->setTurns(300);
        $this->char->level = 20;
        $this->char->save();

        $request = Request::create('/skill/self_use/Unstealth/');
        RequestWrapper::inject($request);
        $controller = new SkillController();
        $controller->update_timer = false;
        $response = $controller->selfUse($this->m_dependencies);
        $this->assertNotInstanceOf(
            RedirectResponse::class,
            $response,
            'A redirect was the outcome for the url: '
            .($response instanceof RedirectResponse ? $response->getTargetUrl() : '')
        );
        $reflection = new \ReflectionProperty(get_class($response), 'data');
        $reflection->setAccessible(true);
        $response_data = $reflection->getValue($response);
        $this->assertEquals('Unstealth', $response_data['act']);
    }

    public function testUsePoisonTouchOnAnotherChar()
    {
        $error = $this->char->setClass('viper');
        $this->char->setTurns(300);
        $this->char->level = 20;
        $this->assertNull($error);
        $this->char->save();
        $initial_health = $this->char2->health;
        $name = $this->char2->name();
        $this->assertNotEmpty($name);
        $this->assertNotEmpty(rawurlencode($name));
        $skillList = new Skill();
        $this->assertTrue($skillList->hasSkill('Poison Touch', $this->char));
        $request = Request::create('/skill/use/Poison%20Touch/'.rawurlencode($name).'/');
        RequestWrapper::inject($request);
        $skill = new SkillController();
        $skill->update_timer = false;
        $response = $skill->useSkill($this->m_dependencies);

        $final_defender = Player::find($this->char2->id());
        $this->assertNotInstanceOf(
            RedirectResponse::class,
            $response,
            'A redirect was the outcome for the url: '
            .($response instanceof RedirectResponse ? $response->getTargetUrl() : '')
        );
        $reflection = new \ReflectionProperty(get_class($response), 'data');
        $reflection->setAccessible(true);
        $response_data = $reflection->getValue($response);
        $this->assertNull($response_data['error']);
        $this->assertEquals('Poison Touch', $response_data['act']);
        $this->assertLessThan($initial_health, $final_defender->health);
    }

    public function testUseSightOnAnotherChar()
    {
        $error = $this->char->setClass('dragon');
        $this->char->setTurns(300);
        $this->char->level = 20;
        $this->assertNull($error);
        $this->char->save();
        $name = $this->char2->name();
        $this->assertNotEmpty($name);
        $this->assertNotEmpty(rawurlencode($name));
        $skillList = new Skill();
        $this->assertTrue($skillList->hasSkill('Sight', $this->char));
        $request = Request::create('/skill/use/Sight/'.rawurlencode($name).'/');
        RequestWrapper::inject($request);
        $skill = new SkillController();
        $skill->update_timer = false;
        $response = $skill->useSkill($this->m_dependencies);

        $this->assertNotInstanceOf(
            RedirectResponse::class,
            $response,
            'An error redirect was sent back for the url: '
            .($response instanceof RedirectResponse ? $response->getTargetUrl() : '')
        );
        $reflection = new \ReflectionProperty(get_class($response), 'data');
        $reflection->setAccessible(true);
        $response_data = $reflection->getValue($response);
        $this->assertNull($response_data['error']);
        $this->assertEquals('Sight', $response_data['act']);
    }

    public function testUseCloneKillOnSelf()
    {
        $error = $this->char->setClass('dragon');
        $this->char->setTurns(300);
        $this->char->level = 20;
        $this->assertNull($error);
        $this->char->save();
        $name = $this->char2->name();
        $this->assertNotEmpty($name);
        $this->assertNotEmpty(rawurlencode($name));
        $request = Request::create('/skill/use/Clone%20Kill/'.rawurlencode($name).'/'.rawurlencode($name).'/');
        RequestWrapper::inject($request);
        $skill = new SkillController();
        $skill->update_timer = false;
        $response = $skill->useSkill($this->m_dependencies);

        $this->assertNotInstanceOf(
            RedirectResponse::class,
            $response,
            'An error redirect was sent back for the url: '
            .($response instanceof RedirectResponse ? $response->getTargetUrl() : '')
        );
        $reflection = new \ReflectionProperty(get_class($response), 'data');
        $reflection->setAccessible(true);
        $response_data = $reflection->getValue($response);
        $this->assertNull($response_data['error']);
        $this->assertNotNull($response_data['generic_skill_result_message']);
        $this->assertGreaterThan(3, mb_strlen($response_data['generic_skill_result_message']));
        $this->assertEquals('Clone Kill', $response_data['act']);
    }

    // TODO: test that self_use of things like Steal error or whatever the right behavior should be?
    // TODO: test that use of skills that aren't part of the users skillset error
    // TODO: test that use of unstealth on another fails
    // TODO: test that use of stealth on another fails.

    public function testUseHealOnSelfAsAHealingCharacter()
    {
        $this->char->setClass('dragon');
        $this->char->setTurns(300);
        $this->char->level = 20;
        // Between 2 and (between half initial health or half of max health)
        // So that a bad getMaxHealth in excess of real initial health doesn't break this test
        $harm_by = max(
            2,
            min(
                (int)floor($this->char->health / 2),
                (int)floor($this->char->getMaxHealth() / 2)
            )
        );
        $this->char->harm($harm_by);
        $this->char->save();

        $initial_health = $this->char->health;
        $this->assertGreaterThan(0, $initial_health, 'Character came back with no health initially!');
        $this->assertGreaterThan($initial_health, $this->char->getMaxHealth());

        RequestWrapper::inject(Request::create('/skill/self_use/Heal/'));
        $controller = new SkillController();
        $controller->update_timer = false;
        $response = $controller->selfUse($this->m_dependencies);

        $this->assertNotInstanceOf(
            RedirectResponse::class,
            $response,
            'A redirect was the outcome for the url: '
            .($response instanceof RedirectResponse ? $response->getTargetUrl() : '')
        );
        $reflection = new \ReflectionProperty(get_class($response), 'data');
        $reflection->setAccessible(true);
        $response_data = $reflection->getValue($response);
        $this->assertEquals('Heal', $response_data['act']);
        $final_char = Player::find($this->char->id());
        $this->assertGreaterThan($initial_health, $final_char->health);
    }

    public function testUseHarmonizeOnSelf()
    {
        $this->char->setTurns(300);
        $this->char->ki = 1000;
        $this->char->level = 20;
        $this->char->harm(floor($this->char->getMaxHealth() / 2));
        $this->char->save();

        $initial_health = $this->char->health;
        $this->assertGreaterThan($initial_health, $this->char->getMaxHealth());

        RequestWrapper::inject(Request::create('/skill/self_use/Harmonize/'));
        $controller = new SkillController();
        $controller->update_timer = false;
        $response = $controller->selfUse($this->m_dependencies);

        $this->assertNotInstanceOf(
            RedirectResponse::class,
            $response,
            'A redirect was the outcome for the url: '
            .($response instanceof RedirectResponse ? $response->getTargetUrl() : '')
        );
        $reflection = new \ReflectionProperty(get_class($response), 'data');
        $reflection->setAccessible(true);
        $response_data = $reflection->getValue($response);
        $this->assertEquals('Harmonize', $response_data['act']);
        $final_char = Player::find($this->char->id());
        $this->assertGreaterThan($initial_health, $final_char->health);
    }
}
