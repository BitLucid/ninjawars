<?php
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use NinjaWars\core\extensions\SessionFactory;
use NinjaWars\core\control\AccountController;
use NinjaWars\core\control\SignupController;
use NinjaWars\core\data\Player;
use NinjaWars\core\data\Account;

/** Account behavior
 *
 * When an account is created, it is initially unconfirmed, but "operational"
 * (only used to delete unwanted accounts, spammers, etc)
 *
 * Login attempts when unconfirmed ask for confirmation.
 *
 * After confirmation, login is allowed.
 *
 * A ninja may start out as non-active, which means that they won't show up in
 * the list until they log in.
 *
 * If they are inactive for long enough, they'll be deactivated, and won't show
 * up in the list until they log in again, at which point their status will be
 * toggled to "active".
 *
 * A player will never have to confirm themselves more than once.
 *
 * Login is all it takes to turn a ninja from "inactive" to "active", and it
 * should happen transparently upon login.
 *
 * Login should also update "last logged in" data, but that's probably better
 * in a different test suite.
 */


/** Tests:
 * Create test account & player, they should start off unconfirmed, operational
 *
 * Try to login with a newly created player, the account should not be able to
 * be logged in (for most emails) confirm an account, test that the database
 * data is toggled login with confirmed account, login should proceed correctly
 * create a new account, confirm it, and then set it inactive. Then perform a
 * login.
 *
 * Test that after login, the ninja is toggled to active.
 */
class TestAccountConfirmation extends PHPUnit_Framework_TestCase {
    // These will be initialized in the test setup.
    public $test_email = null;
    public $test_password = null;
    public $test_ninja_name = null;
    public $test_ninja_id = null;

    /**
     * group accountconf
     */
    function setUp() {
        $_SERVER['REMOTE_ADDR']=isset($_SERVER['REMOTE_ADDR'])? $_SERVER['REMOTE_ADDR'] : '127.0.0.1';
        $this->test_email = TestAccountCreateAndDestroy::$test_email; // Something@example.com probably
        $this->test_password = TestAccountCreateAndDestroy::$test_password;
        $this->test_ninja_name = TestAccountCreateAndDestroy::$test_ninja_name;
        TestAccountCreateAndDestroy::purge_test_accounts($this->test_ninja_name);
        $this->test_ninja_id = TestAccountCreateAndDestroy::create_testing_account();
        SessionFactory::init(new MockArraySessionStorage());
    }

    /**
     * group accountconf
     */
    function tearDown() {
        // Delete test user.
        TestAccountCreateAndDestroy::purge_test_accounts($this->test_ninja_name);
        $session = SessionFactory::getSession();
        $session->invalidate();
    }

    /**
     */
    function testForNinjaNameValidationErrors() {
        $this->assertNotEmpty(username_format_validate('tooooooooooooooolongggggggggggggggggggggggggggggg'),
            'Username not flagged as too long');
        $this->assertNotEmpty(username_format_validate('st')); // Too short
        $this->assertNotEmpty(username_format_validate(''));
        $this->assertNotEmpty(username_format_validate(' '));
        $this->assertNotEmpty(username_format_validate('_underscorestartsit'));
        $this->assertNotEmpty(username_format_validate('underscore-ends-it_'));
        $this->assertNotEmpty(username_format_validate('too____mny----l'));
        $this->assertNotEmpty(username_format_validate('----@#$@#$%@#!$'));
        $this->assertFalse(username_format_validate('acceptable'), 'Basic lowercase alpha name [acceptable] was rejected');
        $this->assertFalse(username_format_validate('ThisIsAcceptable'), 'Basic alpha name [ThisIsAcceptable] was rejected');
    }

    /**
     * group accountconf
     */
    function testForNinjaThatAccountConfirmationProcessAllowsNinjaNamesOfTheRightFormat() {
        $this->assertTrue(!(bool)username_format_validate('tchalvak'), 'Standard all alpha name tchalvak was rejected');
        $this->assertTrue(!(bool)username_format_validate('Beagle'));
        $this->assertTrue(!(bool)username_format_validate('Kzqai'));

        $acceptable_names = [
            'xaz',
            'NameWillBeExactly24Lett',
            'tchalvak',
            'Kzqai',
            'Kakashi66',
            'name_withunderscore',
            'name-withdash',
            'ninjamaster331',
            'Over_Medicated',
            'No_One_Important',
            'murmkuma',
            'XtoxxictantrumX',
            'dragon39540lkjhgfdsa',
            'SasukeMoNo31',
            'SASAGAKURE',
            'TheBlackPhynix',
            'NGkillerdrillNG',
            'BOTDFLUVER22',
            'TheStripedShirtSlasher',
            'sadasdasdasd124123l',
            'L4RR3s222',
            'Dark-Red-EyeZ',
        ];

        foreach ($acceptable_names as $name) {
            $error = username_format_validate($name);
            $this->assertTrue(!(bool)$error, 'Rejected name was: ['.$name.'] and error was ['.$error.']');
        }
    }

    /**
     * group accountconf
     */
    function testThatTestAccountLibActuallyWorksToCreateAndDestroyATestNinja() {
        TestAccountCreateAndDestroy::purge_test_accounts();
        $test_char_id = TestAccountCreateAndDestroy::create_testing_account();
        $this->assertTrue((bool)positive_int($test_char_id));
    }

    /**
     * group accountconf
     */
    function testCreateFullAccountConfirmAndReturnAccountId() {
        $account_id = TestAccountCreateAndDestroy::create_complete_test_account_and_return_id();
        $this->assertTrue((bool)positive_int($account_id));
    }

    /**
     * group accountconf
     */
    function testMakeSureThatNinjaAccountIsOperationalByDefault() {
        $ninja_id = $this->test_ninja_id;
        $this->assertTrue(positive_int($ninja_id) > 0);

        $account_operational = query_item(
            'SELECT operational FROM accounts JOIN account_players ON account_id = _account_id WHERE _player_id = :char_id',
            [':char_id' => $ninja_id]
        );

        $this->assertTrue($account_operational, 'Account is not being set as operational by default when created');
    }

    /**
     * group accountconf
     */
    function testAttemptLoginOfUnconfirmedAccountShouldFail() {
        $email ='noautoconfirm@hotmail.com'; // Create a non-autoconfirmed user
        TestAccountCreateAndDestroy::create_testing_account(false, $email);
        $res = login_user($email, $this->test_password);
        $this->assertFalse($res['success']);
        $this->assertTrue(is_string($res['login_error']));
        $this->assertTrue((bool)$res['login_error'], 'No error returned: '.$res['login_error']);
    }

    /**
     * group accountconf
     */
    function testConfirmAccount() {
        $player = Player::findByName($this->test_ninja_name);
        $account = Account::findByChar($player);
        $account->confirmed = 1;
        $account->save();

        $active_string = query_item(
            'SELECT active FROM accounts JOIN account_players ON account_id = _account_id JOIN players ON player_id = _player_id WHERE players.uname = :uname',
            [':uname' => $this->test_ninja_name]
        );

        $this->assertEquals('1', $active_string);
    }

    /**
     * group accountconf
     */
    function testAuthenticateConfirmedAccountByName() {
        $player = Player::findByName($this->test_ninja_name);
        $player->active = 1;
        $player->save();

        $account = Account::findByChar($player);
        $account->confirmed = 1;
        $account->setOperational(true);
        $account->save();

        $res = authenticate($this->test_ninja_name, $this->test_password, false);
        $this->assertNotEmpty($res); // Should return account_id
        $this->assertNotEmpty($res['account_id']);
        $this->assertNotEmpty($res['account_identity']);
        $this->assertNotEmpty($res['uname']);
        $this->assertNotEmpty($res['player_id']);
    }

    /**
     * group accountconf
     */
    function testLoginConfirmedAccountByName() {
        $player = Player::findByName($this->test_ninja_name);
        $player->active = 1;
        $player->save();

        $account = Account::findByChar($player);
        $account->confirmed = 1;
        $account->setOperational(true);
        $account->save();

        $res = login_user($this->test_ninja_name, $this->test_password);
        $this->assertTrue($res['success'], 'Login by ninja name failed for ['.$this->test_ninja_name.'] with password ['.$this->test_password.'] with login error: ['.$res['login_error'].']');
        $this->assertFalse((bool)$res['login_error']);
    }

    /**
     * group accountconf
     */
    function testLoginConfirmedAccountByEmail() {
        $player = Player::findByName($this->test_ninja_name);
        $player->active = 1;
        $player->save();

        $account = Account::findByChar($player);
        $account->confirmed = 1;
        $account->setOperational(true);
        $account->save();

        $res = login_user($this->test_email, $this->test_password);
        $this->assertTrue($res['success'], 'Login by email failed for confirmed player ['.$this->test_ninja_name.'] with password ['.$this->test_password.'] with login error: ['.$res['login_error'].']');
        $this->assertFalse((bool)$res['login_error']);
    }

    /**
     * group accountconf
     */
    function testLoginConfirmedAccountWithInactivePlayerSucceeds(){
        $player = Player::findByName($this->test_ninja_name);
        $player->active = 0;
        $player->save();

        $account = Account::findByChar($player);
        $account->confirmed = 1;
        $account->setOperational(true);
        $account->save();

        $res = login_user($this->test_email, $this->test_password);
        $this->assertTrue($res['success'], 'Faded-to-inactive player unable to login');
        $this->assertFalse((bool)$res['login_error']);
    }

    /**
     * group accountconf
     */
    function testPauseAccountAndLoginShouldFail() {
        $player = Player::findByName($this->test_ninja_name);
        $player->active = 1;
        $player->save();

        $account = Account::findByChar($player);
        $account->confirmed = 1;
        $account->setOperational(true);
        $account->save();

        $accountController = new AccountController();

        $res = login_user($this->test_email, $this->test_password);
        $this->assertTrue($res['success'], 'Login should be successful when account is new');

        // Fully pause the account, make the operational bit = false
        $player->active = 0;
        $player->save();

        $account->setOperational(false);
        $account->save();

        $res = login_user($this->test_email, $this->test_password);

        $this->assertFalse($res['success'], 'Login should not be successful when account is paused');
        $this->assertTrue(is_string($res['login_error']));
        $this->assertTrue((bool)$res['login_error']);
    }

    /**
     * group accountconf
     */
    function testPreconfirmEmailsReturnRightResultForGmailHotmailAndWildcardEmails(){
        $preconfirm_emails = ['test@gmail.com', 'test@example.com', 'test@russia.com'];
        $no_preconfirm_emails = ['test@hotmail.com', "O'brian@yahoo.com"];

        foreach ($preconfirm_emails as $email) {
            $this->assertTrue((bool)SignupController::preconfirm_some_emails($email));
        }

        foreach ($no_preconfirm_emails as $email) {
            $this->assertFalse((bool)SignupController::preconfirm_some_emails($email));
        }
    }

    /**
     * group accountconf
     */
    function testThatAccountConfirmationProcessRejectsNinjaNamesOfTheWrongFormat() {
        // Same requirements as above, here we test that bad names are rejected.
        $bad_names = [
            'xz',
            'bo',
            '69numfirst',
            'underscorelast_',
            'specialChar##',
            '@!#$#$^#$@#',
            'double__underscore',
            'double--dash',
        ];

        foreach ($bad_names as $name) {
            $this->assertFalse(!(bool)username_format_validate($name));
        }
    }

}
