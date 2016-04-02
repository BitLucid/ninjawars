<?php
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use NinjaWars\core\data\Account;
use NinjaWars\core\data\Player;
use NinjaWars\core\control\SignupController;
use NinjaWars\core\extensions\SessionFactory;
use NinjaWars\core\environment\RequestWrapper;

class SignupControllerTest extends PHPUnit_Framework_TestCase {
    private $char_id;

	public function setUp() {
		SessionFactory::init(new MockArraySessionStorage());
        $this->char_id = TestAccountCreateAndDestroy::create_testing_account();
		SessionFactory::getSession()->set('player_id', $this->char_id);
	}

	public function tearDown() {
        $session = SessionFactory::getSession();
        $session->invalidate();
    }

    public function testInstantiation() {
        $controller = new SignupController();
        $this->assertInstanceOf('NinjaWars\core\control\SignupController', $controller);
    }

    public function testBlacklist() {
        $this->assertInternalType('array', SignupController::getBlacklistedEmails());
    }

    public function testWhitelist() {
        $this->assertInternalType('array', SignupController::getWhitelistedEmails());
    }

    public function testIndexRuns() {
        $controller = new SignupController();
        $response = $controller->index();
        $this->assertNotEmpty($response);
    }

    public function testEmptySignupFails() {
        RequestWrapper::inject(new Request([]));
        $controller = new SignupController();
        $response = $controller->signup();
        $this->assertNotEmpty($response['parts']['error']);
    }

    /**
     * Test that the error message is correct for mismatched passwords
     */
    public function testMismatchPasswordFailsCorrectly() {
        RequestWrapper::inject(new Request([
            'key'   => 'password1',
            'cpass' => 'password2',
        ]));

        $controller = new SignupController();
        $response = $controller->signup();
        $this->assertNotEmpty($response['parts']['error']);
        $this->assertContains('match', $response['parts']['error']);
    }

    /**
     * Test that the error message is correct for missing name
     */
    public function testMissingNameFailsCorrectly() {
        RequestWrapper::inject(new Request([
            'key'        => 'password1',
            'cpass'      => 'password1',
            'send_email' => 'new@localhost',
        ]));

        $controller = new SignupController();
        $response = $controller->signup();
        $this->assertNotEmpty($response['parts']['error']);
        $this->assertContains('all the nece', $response['parts']['error']);
    }

    /**
     * Test that the error message is correct
     */
    public function testInvalidNameFailsCorrectly() {
        RequestWrapper::inject(new Request([
            'key'        => 'password1',
            'cpass'      => 'password1',
            'send_email' => 'new@localhost',
            'send_name'  => '@-/.=+-09122198408357&^@Q  *&#^(!',
        ]));

        $controller = new SignupController();
        $response = $controller->signup();
        $this->assertNotEmpty($response['parts']['error']);
        $this->assertContains('Ninja name:', $response['parts']['error']);
    }

    /**
     * Test that the error message is correct
     */
    public function testInvalidPasswordFailsCorrectly() {
        RequestWrapper::inject(new Request([
            'key'        => 'p',
            'cpass'      => 'p',
            'send_email' => 'new@localhost',
            'send_name'  => 'KnownGood',
        ]));

        $controller = new SignupController();
        $response = $controller->signup();
        $this->assertNotEmpty($response['parts']['error']);
        $this->assertContains('Passwords must', $response['parts']['error']);
    }

    /**
     * Test that the error message is correct
     */
    public function testDuplicateEmailFailsCorrectly() {
        $account = Account::findByChar(Player::find($this->char_id));
        $original_email = $account->active_email;
        $account->active_email = 'new@local.host';
        $account->save();

        RequestWrapper::inject(new Request([
            'key'        => 'password1',
            'cpass'      => 'password1',
            'send_email' => 'new@local.host',
            'send_name'  => 'KnownGood',
        ]));

        $controller = new SignupController();
        $response = $controller->signup();

        $account->active_email = $original_email;
        $account->save();

        $this->assertNotEmpty($response['parts']['error']);
        $this->assertContains('using that email', $response['parts']['error']);
    }

    /**
     * Test that the error message is correct
     */
    public function testInvalidEmailFailsCorrectly() {
        RequestWrapper::inject(new Request([
            'key'        => 'password1',
            'cpass'      => 'password1',
            'send_email' => 'new email',
            'send_name'  => 'KnownGood',
        ]));

        $controller = new SignupController();
        $response = $controller->signup();
        $this->assertNotEmpty($response['parts']['error']);
        $this->assertContains('to be valid', $response['parts']['error']);
    }

    /**
     * Test that the error message is correct
     */
    public function testDupeNameFailsCorrectly() {
        $player = Player::find($this->char_id);
        $player->vo->uname = 'KnownGood';
        $player->save();

        RequestWrapper::inject(new Request([
            'key'        => 'password1',
            'cpass'      => 'password1',
            'send_email' => 'new@local.host',
            'send_name'  => 'KnownGood',
        ]));

        $controller = new SignupController();
        $response = $controller->signup();
        $this->assertNotEmpty($response['parts']['error']);
        $this->assertContains('already in use', $response['parts']['error']);
    }

    /**
     * Test that the error message is correct
     */
    public function testReservedNameFailsCorrectly() {
        RequestWrapper::inject(new Request([
            'key'        => 'password1',
            'cpass'      => 'password1',
            'send_email' => 'new@local.host',
            'send_name'  => 'SysMsg',
        ]));

        $controller = new SignupController();
        $response = $controller->signup();
        $this->assertNotEmpty($response['parts']['error']);
        $this->assertContains('already in use', $response['parts']['error']);
    }

    public function testInvalidClassFailsCorrectly() {
        RequestWrapper::inject(new Request([
            'key'        => 'password1',
            'cpass'      => 'password1',
            'send_email' => 'new@local.host',
            'send_name'  => 'KnownGood',
            'send_class' => 'KnownBad',
        ]));

        $controller = new SignupController();
        $response = $controller->signup();
        $this->assertNotEmpty($response['parts']['error']);
        $this->assertContains('proper class', $response['parts']['error']);
    }

    public function testSuccessfulSignup() {
        $uname = 'KnownGood';
        $email = 'new@local.host';

        RequestWrapper::inject(new Request([
            'key'        => 'password1',
            'cpass'      => 'password1',
            'send_email' => $email,
            'send_name'  => $uname,
        ]));

        $controller = new SignupController();
        $response = $controller->signup();

        $account = Account::findByEmail($email);
        $player = Player::findByName($uname);

        $query_relationship = 'SELECT count(*) FROM account_players WHERE _account_id = :id1 AND _player_id = :id2';

        if ($account && $player) {
            $relationship_count = query_item($query_relationship, [':id1' => $account->id(), ':id2' => $player->id()]);
        } else {
            $relationship_count = 0;
        }

        $delete_player = 'DELETE FROM players WHERE player_id = :id';
        $delete_account = 'DELETE FROM accounts WHERE account_id = :id';
        $delete_relationship = 'DELETE FROM account_players WHERE _account_id = :id1 OR _player_id = :id2';

        query($delete_player, [':id' => $player->id()]);
        query($delete_account, [':id' => $account->id()]);
        query($delete_relationship, [':id1' => $account->id(), ':id2' => $player->id()]);

        $this->assertTrue($response['parts']['submit_successful']);
        $this->assertEquals($relationship_count, 1);
    }
}
