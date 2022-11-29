<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use NinjaWars\core\data\Account;
use NinjaWars\core\data\Player;
use NinjaWars\core\control\SignupController;
use NinjaWars\core\extensions\SessionFactory;
use NinjaWars\core\environment\RequestWrapper;

class SignupControllerTest extends NWTest {
    private $char_id;
    private $fake_email = 'new@local.host';
    private $fake_email_alt = 'new2@example.host';
    private $fake_user = 'signup-test-user';
    private $known_good_user = 'KnownGood';

    public function setUp(): void {
        parent::setUp();
        SessionFactory::init(new MockArraySessionStorage());
        TestAccountCreateAndDestroy::destroy(null, $this->fake_email_alt);
        TestAccountCreateAndDestroy::destroy(null, $this->fake_email);
        TestAccountCreateAndDestroy::destroy($this->fake_user);
        TestAccountCreateAndDestroy::destroy($this->known_good_user);
        $this->char_id = TestAccountCreateAndDestroy::create_testing_account($this->fake_user);
        SessionFactory::getSession()->set('player_id', $this->char_id);
    }

    public function tearDown(): void {
        TestAccountCreateAndDestroy::destroy($this->fake_user);
        TestAccountCreateAndDestroy::destroy($this->fake_email_alt);
        $session = SessionFactory::getSession();
        $session->invalidate();
        parent::tearDown();
    }

    public function testInstantiation() {
        $controller = new SignupController();
        $this->assertInstanceOf(SignupController::class, $controller);
    }

    public function testBlacklist() {
        $this->assertIsArray(SignupController::getBlacklistedEmails());
    }

    public function testWhitelist() {
        $this->assertIsArray(SignupController::getWhitelistedEmails());
    }

    public function testSignupIndexRuns() {
        $controller = new SignupController();
        $response = $controller->index($this->m_dependencies);
        $this->assertNotEmpty($response);
    }

    public function testSignupIndexRunsEvenIfLoggedOut() {
        $controller = new SignupController();
        $response = $controller->index($this->mockLogout());
        $this->assertNotEmpty($response);
    }

    public function testEmptySignupFails() {
        RequestWrapper::inject(new Request([]));
        $controller = new SignupController();
        $response = $controller->signup($this->m_dependencies);
        $reflection = new \ReflectionProperty(get_class($response), 'data');
        $reflection->setAccessible(true);
        $response_data = $reflection->getValue($response);
        $this->assertNotEmpty($response_data['error']);
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
        $response = $controller->signup($this->m_dependencies);
        $reflection = new \ReflectionProperty(get_class($response), 'data');
        $reflection->setAccessible(true);
        $response_data = $reflection->getValue($response);
        $this->assertNotEmpty($response_data['error']);
        $this->assertStringContainsString('match', $response_data['error']);
    }

    /**
     * Test that the error message is correct for missing name
     */
    public function testMissingNameFailsCorrectly() {
        RequestWrapper::inject(new Request([
            'key'        => 'password1',
            'cpass'      => 'password1',
            'send_email' => $this->fake_email,
        ]));

        $controller = new SignupController();
        $response = $controller->signup($this->m_dependencies);
        $reflection = new \ReflectionProperty(get_class($response), 'data');
        $reflection->setAccessible(true);
        $response_data = $reflection->getValue($response);
        $this->assertNotEmpty($response_data['error']);
        $this->assertStringContainsString('all the nece', $response_data['error']);
    }

    /**
     * Test that the error message is correct
     */
    public function testInvalidNameFailsCorrectly() {
        RequestWrapper::inject(new Request([
            'key'        => 'password1',
            'cpass'      => 'password1',
            'send_email' => $this->fake_email,
            'send_name'  => '@-/.=+-09122198408357&^@Q  *&#^(!',
        ]));

        $controller = new SignupController();
        $response = $controller->signup($this->m_dependencies);
        $reflection = new \ReflectionProperty(get_class($response), 'data');
        $reflection->setAccessible(true);
        $response_data = $reflection->getValue($response);
        $this->assertNotEmpty($response_data['error']);
        $this->assertStringContainsString('Ninja name:', $response_data['error']);
    }

    /**
     * Test that the error message is correct
     */
    public function testInvalidPasswordFailsCorrectly() {
        RequestWrapper::inject(new Request([
            'key'        => 'p',
            'cpass'      => 'p',
            'send_email' => $this->fake_email,
            'send_name'  => $this->known_good_user,
        ]));

        $controller = new SignupController();
        $response = $controller->signup($this->m_dependencies);
        $reflection = new \ReflectionProperty(get_class($response), 'data');
        $reflection->setAccessible(true);
        $response_data = $reflection->getValue($response);
        $this->assertNotEmpty($response_data['error']);
        $this->assertStringContainsString('Passwords must', $response_data['error']);
    }

    /**
     * Test that the error message is correct
     */
    public function testDuplicateEmailFailsCorrectly() {
        $account = Account::findByChar(Player::find($this->char_id));
        $original_email = $account->active_email;
        $account->active_email = $this->fake_email;
        $account->save();

        RequestWrapper::inject(new Request([
            'key'        => 'password1',
            'cpass'      => 'password1',
            'send_email' => $this->fake_email,
            'send_name'  => $this->known_good_user,
        ]));

        $controller = new SignupController();
        $response = $controller->signup($this->m_dependencies);

        $account->active_email = $original_email;
        $account->save();

        $reflection = new \ReflectionProperty(get_class($response), 'data');
        $reflection->setAccessible(true);
        $response_data = $reflection->getValue($response);
        $this->assertNotEmpty($response_data['error']);
        $this->assertStringContainsString('using that email', $response_data['error']);
    }

    /**
     * Test that the error message is correct
     */
    public function testInvalidEmailFailsCorrectly() {
        RequestWrapper::inject(new Request([
            'key'        => 'password1',
            'cpass'      => 'password1',
            'send_email' => 'new email',
            'send_name'  => $this->known_good_user,
        ]));

        $controller = new SignupController();
        $response = $controller->signup($this->m_dependencies);
        $reflection = new \ReflectionProperty(get_class($response), 'data');
        $reflection->setAccessible(true);
        $response_data = $reflection->getValue($response);
        $this->assertNotEmpty($response_data['error']);
        $this->assertStringContainsString('to be valid', $response_data['error']);
    }

    /**
     * Test that the error message is correct
     */
    public function testDupeNameFailsCorrectly() {
        // Change name of test user to a known expected username
        $player = Player::find($this->char_id);
        $player->uname = $this->known_good_user;
        $player->save(); // Save with that new username

        // Now try to sign up as the same new username
        RequestWrapper::inject(new Request([
            'key'        => 'password1',
            'cpass'      => 'password1',
            'send_email' => $this->fake_email,
            'send_name'  => $this->known_good_user,
        ]));

        $controller = new SignupController();
        $response = $controller->signup($this->m_dependencies);
        $reflection = new \ReflectionProperty(get_class($response), 'data');
        $reflection->setAccessible(true);
        $response_data = $reflection->getValue($response);
        TestAccountCreateAndDestroy::destroy($this->known_good_user);
        $this->assertNotEmpty($response_data['error']);
        $this->assertStringContainsString('already in use', $response_data['error']);
    }

    /**
     * Test that the error message is correct
     */
    public function testReservedNameFailsCorrectly() {
        RequestWrapper::inject(new Request([
            'key'        => 'password1',
            'cpass'      => 'password1',
            'send_email' => $this->fake_email_alt,
            'send_name'  => 'SysMsg',
        ]));

        $controller = new SignupController();
        $response = $controller->signup($this->m_dependencies);
        $reflection = new \ReflectionProperty(get_class($response), 'data');
        $reflection->setAccessible(true);
        $response_data = $reflection->getValue($response);
        $this->assertNotEmpty($response_data['error']);
        $this->assertStringContainsString('already', $response_data['error']);
    }

    public function testInvalidClassFailsCorrectly() {
        RequestWrapper::inject(new Request([
            'key'        => 'password1',
            'cpass'      => 'password1',
            'send_email' => $this->fake_email,
            'send_name'  => 'KnownClassGood',
            'send_class' => 'KnownBad',
        ]));

        $controller = new SignupController();
        $response = $controller->signup($this->m_dependencies);
        $reflection = new \ReflectionProperty(get_class($response), 'data');
        $reflection->setAccessible(true);
        $response_data = $reflection->getValue($response);
        $this->assertNotEmpty($response_data['error']);
        $this->assertStringContainsString('proper class', $response_data['error']);
    }

    public function testSuccessfulSignup() {
        $uname = 'TestAllowedSignup';
        $email = $this->fake_email;

        RequestWrapper::inject(new Request([
            'key'        => 'password1',
            'cpass'      => 'password1',
            'send_email' => $email,
            'send_name'  => $uname,
        ]));

        $controller = new SignupController();
        $response = $controller->signup($this->m_dependencies);

        $account = Account::findByEmail($email);
        $player = Player::findByName($uname);

        $this->assertNotNull($player, 'Player not found after successful signup');
        $this->assertNotNull($account, 'Account not found after successful signup.');

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

        $reflection = new \ReflectionProperty(get_class($response), 'data');
        $reflection->setAccessible(true);
        $response_data = $reflection->getValue($response);
        $this->assertTrue($response_data['submit_successful'], 'Signup() returned error: '.$response_data['error']);
        $this->assertEquals($relationship_count, 1);
    }

    public function testSuccessfulSignupResultsInNoConfirmation() {
        $uname = $this->known_good_user;
        $email = 'thisshouldnevergotoarealperson77748348@hotmail.com';
        // Due to the nature of hotmail, hotmail emails are listed
        // such that they will not be preconfirmed.  This leaves an account needing confirmation.

        RequestWrapper::inject(new Request([
            'key'        => 'password1',
            'cpass'      => 'password1',
            'send_email' => $email,
            'send_name'  => $uname,
        ]));

        $controller = new SignupController();
        $response = $controller->signup($this->m_dependencies);

        $account = Account::findByEmail($email);
        $player = Player::findByName($uname);

        $this->assertNotNull($player, 'Player was not created');
        $this->assertNotNull($account, 'Account was not created');

        $query_relationship = 'SELECT count(*) FROM account_players WHERE _account_id = :id1 AND _player_id = :id2';
        $account_unconfirmed = null;

        if ($account && $player) {
            $relationship_count = query_item($query_relationship, [':id1' => $account->id(), ':id2' => $player->id()]);
            $account_unconfirmed = !$account->isConfirmed();
        } else {
            $relationship_count = 0;
        }

        $delete_player = 'DELETE FROM players WHERE player_id = :id';
        $delete_account = 'DELETE FROM accounts WHERE account_id = :id';
        $delete_relationship = 'DELETE FROM account_players WHERE _account_id = :id1 OR _player_id = :id2';

        query($delete_player, [':id' => $player->id()]);
        query($delete_account, [':id' => $account->id()]);
        query($delete_relationship, [':id1' => $account->id(), ':id2' => $player->id()]);

        $reflection = new \ReflectionProperty(get_class($response), 'data');
        $reflection->setAccessible(true);
        $response_data = $reflection->getValue($response);
        $this->assertTrue($response_data['submit_successful'], 'Signup() returned error: '.$response_data['error']);
        $this->assertEquals($relationship_count, 1);
        $this->assertTrue($account_unconfirmed, 'Account was not found to be unconfirmed after signup...');
    }
}
