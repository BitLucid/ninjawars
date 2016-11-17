<?php
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use NinjaWars\core\environment\RequestWrapper;
use NinjaWars\core\extensions\SessionFactory;
use NinjaWars\core\control\DoshinController;
use NinjaWars\core\data\Player;

class DoshinControllerTest extends NWTest {
    public function setUp() {
        parent::setUp();
        // Mock the post request.
        $request = new Request([], []);
        RequestWrapper::inject($request);
        $this->login();
    }

    public function tearDown() {
        RequestWrapper::inject(new Request([]));
        $this->loginTearDown();
        parent::tearDown();
    }

    public function testInstantiateDoshinController() {
        $doshin = new DoshinController();
        $this->assertInstanceOf('NinjaWars\core\control\DoshinController', $doshin);
    }

    public function testDoshinIndex() {
        $doshin = new DoshinController();
        $output = $doshin->index($this->m_dependencies);
        $this->assertNotEmpty($output);
    }

    public function testDoshinOfferBounty() {
        $doshin = new DoshinController();
        $output = $doshin->offerBounty($this->m_dependencies);
        $this->assertNotEmpty($output);
    }

    public function testBribeCallInDoshinController() {
        $doshin = new DoshinController();
        $output = $doshin->offerBounty($this->m_dependencies);
        $this->assertNotEmpty($output);
    }

    public function testDoshinOfferSomeBountyOnATestPlayer() {
        $target_id = TestAccountCreateAndDestroy::create_alternate_testing_account(true);
        $this->char->setGold(434343);
        $this->char->save();
        $target = Player::find($target_id);

        $request = new Request([
            'target' => $target->name(),
            'amount' => 600
        ]);

        RequestWrapper::inject($request);

        $doshin = new DoshinController();
        $doshin->offerBounty($this->m_dependencies);
        $player = Player::find($target->id());
        $new_bounty = $player->bounty;
        TestAccountCreateAndDestroy::destroy();

        $this->assertEquals(600, $new_bounty);
    }

    public function testBribeDownABounty() {
        $char_id = $this->char->id();
        $target_id = TestAccountCreateAndDestroy::char_id_2();
        $this->char->setGold(434343);
        $this->char->save();

        $this->char->setBounty(400);
        $this->char->save();
        $this->char = Player::find($char_id);
        $this->assertEquals(400, $this->char->bounty);

        $request = new Request([
            'bribe'=>300
        ]);
        RequestWrapper::inject($request);

        $doshin = new DoshinController();
        $doshin->bribe($this->m_dependencies);

        $pulled_char = Player::find($char_id);

        $current_bounty = $pulled_char->bounty;

        // Bounty should be less now

        $this->assertLessThan(400, $current_bounty);
        $this->assertGreaterThan(0, $current_bounty);
    }

    public function testOfferOfBadNegativeBribe() {
        $request = new Request(['bribe'=>-40]);
        RequestWrapper::inject($request);

        $bounty_set = 4444;
        $initial_gold = 7777;
        $this->char->setBounty($bounty_set);
        $this->char->setGold($initial_gold);
        $this->char->save();

        $doshin = new DoshinController();
        $doshin->bribe($this->m_dependencies);
        $final_char = Player::find($this->char->id());
        $this->assertLessThan(7777, $final_char->gold);
        $modified_bounty = $final_char->bounty;
        $this->assertLessThan($bounty_set, $modified_bounty);
        $this->assertGreaterThan(0, $modified_bounty);
    }
}
