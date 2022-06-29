<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use NinjaWars\core\environment\RequestWrapper;
use NinjaWars\core\control\WorkController;
use NinjaWars\core\extensions\SessionFactory;

class WorkControllerTest extends NWTest
{
    public function setUp(): void
    {
        parent::setUp();
        // Mock the post request.
        $request = new Request([], ['worked'=>10]);
        RequestWrapper::inject($request);
        SessionFactory::init(new MockArraySessionStorage());
    }

    public function tearDown(): void
    {
        RequestWrapper::inject(new Request([]));
        $session = SessionFactory::getSession();
        $session->invalidate();
        TestAccountCreateAndDestroy::destroy();
        parent::tearDown();
    }

    public function testWorkControllerCanBeInstantiatedWithoutError()
    {
        $cont = new WorkController();
        $this->assertInstanceOf(WorkController::class, $cont);
    }

    public function testWorkIndexDoesNotError()
    {
        $work = new WorkController();
        $work_response = $work->index($this->m_dependencies);
        $this->assertNotEmpty($work_response);
    }


    public function testWorkIndexCanRenderEvenLoggedOut()
    {
        $work = new WorkController();
        $work_response = $work->index($this->mockLogout());
        $this->assertNotEmpty($work_response);
    }

    public function testLargeWorkRequestWithoutEnoughTurnsIsRejected()
    {
        $this->char = TestAccountCreateAndDestroy::char();
        SessionFactory::getSession()->set('player_id', $this->char->id());
        $request = new Request([], ['worked'=>999]);
        RequestWrapper::inject($request);
        $work = new WorkController();
        $response = $work->requestWork($this->m_dependencies);
        $reflection = new \ReflectionProperty(get_class($response), 'data');
        $reflection->setAccessible(true);
        $response_data = $reflection->getValue($response);
        $earned_gold = $response_data['earned_gold'];
        $this->assertTrue($response_data['not_enough_energy']);
        $this->assertEquals('0', $earned_gold);
    }

    public function testCapTurnsPossibleToWorkFor()
    {
        $this->char = TestAccountCreateAndDestroy::char();
        SessionFactory::getSession()->set('player_id', $this->char->id());
        $request = new Request([], ['worked'=>99977777]);
        RequestWrapper::inject($request);
        $work = new WorkController();
        $response = $work->requestWork($this->m_dependencies);
        $reflection = new \ReflectionProperty(get_class($response), 'data');
        $reflection->setAccessible(true);
        $response_data = $reflection->getValue($response);
        $earned_gold = $response_data['earned_gold'];
        $this->assertTrue($response_data['not_enough_energy']);
        $this->assertEquals('0', $earned_gold);
    }

    public function testWorkDoesNothingWithNegativeWorkRequest()
    {
        // Note that this had to have an active logged in character to not just get an ignored result of "0" gold.
        $this->char = TestAccountCreateAndDestroy::char();
        SessionFactory::getSession()->set('player_id', $this->char->id());
        $request = new Request([], ['worked'=>-999]);
        RequestWrapper::inject($request);
        $work = new WorkController();
        $response = $work->requestWork($this->m_dependencies);
        $reflection = new \ReflectionProperty(get_class($response), 'data');
        $reflection->setAccessible(true);
        $response_data = $reflection->getValue($response);
        $earned_gold = $response_data['earned_gold'];
        $this->assertEquals("0", $earned_gold);
    }
}
