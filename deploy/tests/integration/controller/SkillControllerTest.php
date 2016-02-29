<?php
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use NinjaWars\core\environment\RequestWrapper;
use NinjaWars\core\control\SkillController;
use NinjaWars\core\extensions\SessionFactory;

class SkillControllerTest extends PHPUnit_Framework_TestCase {
	public function setUp() {
        $this->char = TestAccountCreateAndDestroy::char();
        $session = SessionFactory::getSession();
        $session->set('player_id', $this->char->id()); // Mock the login.
        // Mock the post request.
        $request = new Request([], []);
        RequestWrapper::inject($request);
		SessionFactory::init(new MockArraySessionStorage());
	}

	public function tearDown() {
        $this->char = null;
        TestAccountCreateAndDestroy::destroy();
        RequestWrapper::inject(new Request([]));
        $session = SessionFactory::getSession();
        $session->invalidate();
    }

    public function testSkillIndexDoesNotError() {
        var_dump(self_char_id());
        $skill = new SkillController();
        $skill_outcome = $skill->index();
        $this->assertNotEmpty($skill_outcome);
    }

    public function testLoggedInSkillsDisplay(){
        var_dump(self_char_id());
        $skill = new SkillController();
        $skill_outcome = $skill->index();
        $this->assertEquals('bob', $skill_outcome['parts']['error']);
    }


}
