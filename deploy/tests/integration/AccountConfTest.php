<?php

use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use NinjaWars\core\extensions\SessionFactory;
use NinjaWars\core\Filter;
use NinjaWars\core\control\AccountController;
use NinjaWars\core\control\SignupController;
use NinjaWars\core\control\LoginController;
use NinjaWars\core\data\Player;
use NinjaWars\core\data\Account;
use NinjaWars\core\environment\RequestWrapper;
use Symfony\Component\HttpFoundation\Request;

/** SEE ALSO AccountTest */

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
class AccountConfTest extends NWTest
{
    // These will be initialized in the test setup.
    public $test_email = null;
    public $test_password = null;
    public $test_ninja_name = null;
    public $test_ninja_id = null;
    public $temp_test_email = 'noautoconfirm@hotmail.com';


    public function setUp(): void
    {
        parent::setUp();
        $_SERVER['REMOTE_ADDR'] = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1';
        $this->test_email = TestAccountCreateAndDestroy::$test_email; // Something@example.com probably
        $this->test_password = TestAccountCreateAndDestroy::$test_password;
        $this->test_ninja_name = TestAccountCreateAndDestroy::$test_ninja_name;
        TestAccountCreateAndDestroy::purge_test_accounts($this->test_ninja_name, $this->temp_test_email);
        $this->test_ninja_id = TestAccountCreateAndDestroy::create_testing_account();
        SessionFactory::init(new MockArraySessionStorage());
    }


    public function tearDown(): void
    {
        // Delete test user.
        TestAccountCreateAndDestroy::purge_test_accounts($this->test_ninja_name, $this->temp_test_email);
        $session = SessionFactory::getSession();
        $session->invalidate();
        parent::tearDown();
    }

    /**
     */
    public function testForNinjaNameValidationErrors()
    {
        $this->assertNotEmpty(
            Account::usernameIsValid('tooooooooooooooolongggggggggggggggggggggggggggggg'),
            'Username not flagged as too long'
        );
        $this->assertNotEmpty(Account::usernameIsValid('st')); // Too short
        $this->assertNotEmpty(Account::usernameIsValid(''));
        $this->assertNotEmpty(Account::usernameIsValid(' '));
        $this->assertNotEmpty(Account::usernameIsValid('5555numberstarts'));
        $this->assertNotEmpty(Account::usernameIsValid('_underscorestartsit'));
        $this->assertNotEmpty(Account::usernameIsValid('underscore-ends-it_'));
        $this->assertNotEmpty(Account::usernameIsValid('too____mny----l'));
        $this->assertNotEmpty(Account::usernameIsValid('----@#$@#$%@#!$'));
        $this->assertFalse(Account::usernameIsValid('acceptable'), 'Basic lowercase alpha name [acceptable] was rejected');
        $this->assertFalse(Account::usernameIsValid('ThisIsAcceptable'), 'Basic alpha name [ThisIsAcceptable] was rejected');
    }


    public function testForNinjaThatAccountConfirmationProcessAllowsNinjaNamesOfTheRightFormat()
    {
        $this->assertTrue(!(bool)Account::usernameIsValid('tchalvak'), 'Standard all alpha name tchalvak was rejected');
        $this->assertTrue(!(bool)Account::usernameIsValid('Beagle'));
        $this->assertTrue(!(bool)Account::usernameIsValid('Kzqai'));

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
            $error = Account::usernameIsValid($name);
            $this->assertTrue(!(bool)$error, 'Rejected name was: ['.$name.'] and error was ['.$error.']');
        }
    }


    public function testThatTestAccountLibActuallyWorksToCreateAndDestroyATestNinja()
    {
        TestAccountCreateAndDestroy::purge_test_accounts();
        $test_char_id = TestAccountCreateAndDestroy::create_testing_account();
        $this->assertTrue((bool)Filter::toNonNegativeInt($test_char_id));
    }


    public function testCreateFullAccountConfirmAndReturnAccountId()
    {
        $account_id = TestAccountCreateAndDestroy::create_complete_test_account_and_return_id();
        $this->assertTrue((bool)Filter::toNonNegativeInt($account_id));
    }


    public function testMakeSureThatNinjaAccountIsOperationalByDefault()
    {
        $ninja_id = $this->test_ninja_id;
        $this->assertTrue(Filter::toNonNegativeInt($ninja_id) > 0);

        $account_operational = query_item(
            'SELECT operational FROM accounts JOIN account_players ON account_id = _account_id WHERE _player_id = :char_id',
            [':char_id' => $ninja_id]
        );

        $this->assertEquals('1', $account_operational, 'Account is not being set as operational by default when created');
    }


    public function testAttemptLoginOfUnconfirmedAccountShouldFail()
    {
        $email = $this->temp_test_email; // Create a non-autoconfirmed user using hotmail, probably
        TestAccountCreateAndDestroy::create_testing_account(false, ['email' => $email]);

        RequestWrapper::inject(new Request([]));
        $account = Account::findByEmail($email);
        $controller = new LoginController();
        $res = $controller->performLogin($email, $this->test_password);
        $this->assertNotEmpty($account, 'No account was created');
        $this->assertNotEquals(true, $account->confirmed, 'Account was confirmed despite not using an autoconfirm email');
        $this->assertNotEquals('', $res, 'No error string returned from login returned, indicating the login was able to continue');
        $this->assertNotEmpty($res, 'No error string returned from login returned, indicating the login was able to continue');
    }


    public function testConfirmAccount()
    {
        $player = Player::findByName($this->test_ninja_name);
        $account = Account::findByChar($player);
        $account->setConfirmed(true);
        $account->save();

        $active_string = query_item(
            'SELECT active FROM accounts JOIN account_players ON account_id = _account_id JOIN players ON player_id = _player_id WHERE players.uname = :uname',
            [':uname' => $this->test_ninja_name]
        );

        $this->assertEquals('1', $active_string);
    }



    public function testLoginConfirmedAccountByName()
    {
        $player = Player::findByName($this->test_ninja_name);
        $player->active = 1;
        $player->save();

        $account = Account::findByChar($player);
        $account->confirmed = 1;
        $account->setOperational(true);
        $account->save();

        $res = $account->authenticate($this->test_password);
        $this->assertTrue($res);

        $controller = new LoginController();
        $res = $controller->performLogin($this->test_ninja_name, $this->test_password);
        $this->assertEmpty($res, 'Login by ninja name failed for ['.$this->test_ninja_name.'] with password ['.$this->test_password.'] with login error: ['.$res.']');
    }

    public function testLoginFailureOnAccountByName()
    {
        $player = Player::findByName($this->test_ninja_name);
        $player->active = 1;
        $player->save();

        $account = Account::findByChar($player);
        $account->confirmed = 1;
        $account->setOperational(true);
        $account->save();

        $res = $account->authenticate('invalid_password');
        $this->assertFalse($res);
    }


    public function testLoginConfirmedAccountByEmail()
    {
        $player = Player::findByName($this->test_ninja_name);
        $player->active = 1;
        $player->save();

        $account = Account::findByChar($player);
        $account->confirmed = 1;
        $account->setOperational(true);
        $account->save();

        $controller = new LoginController();
        $res = $controller->performLogin($this->test_email, $this->test_password);
        $this->assertEmpty($res, 'Login by email failed for confirmed player ['.$this->test_ninja_name.'] with password ['.$this->test_password.'] with login error: ['.$res.']');
    }


    public function testLoginConfirmedAccountWithInactivePlayerSucceeds()
    {
        $player = Player::findByName($this->test_ninja_name);
        $player->active = 0;
        $player->save();

        $account = Account::findByChar($player);
        $account->confirmed = 1;
        $account->setOperational(true);
        $account->save();

        $controller = new LoginController();
        $res = $controller->performLogin($this->test_email, $this->test_password);
        $this->assertEmpty($res, 'Faded-to-inactive player unable to login');
    }


    public function testPauseAccountAndLoginShouldFail()
    {
        $player = Player::findByName($this->test_ninja_name);
        $player->active = 1;
        $player->save();

        $account = Account::findByChar($player);
        $account->confirmed = 1;
        $account->setOperational(true);
        $account->save();

        $accountController = new AccountController();

        $controller = new LoginController();
        $res = $controller->performLogin($this->test_email, $this->test_password);
        $this->assertEmpty($res, 'Login should be successful when account is new');

        // Fully pause the account, make the operational bit = false
        $player->active = 0;
        $player->save();

        $account->setOperational(false);
        $account->save();

        $res = $controller->performLogin($this->test_email, $this->test_password);

        $this->assertNotEmpty($res, 'Login should not be successful when account is paused');
    }


    public function testPreconfirmEmailsReturnRightResultForGmailHotmailAndWildcardEmails()
    {
        $preconfirm_emails = ['test@gmail.com', 'test@example.com', 'test@russia.com'];
        $no_preconfirm_emails = ['test@hotmail.com', "O'brian@yahoo.com"];

        foreach ($preconfirm_emails as $email) {
            $this->assertTrue((bool)SignupController::preconfirm_some_emails($email));
        }

        foreach ($no_preconfirm_emails as $email) {
            $this->assertFalse((bool)SignupController::preconfirm_some_emails($email));
        }
    }


    public function testThatAccountConfirmationProcessRejectsNinjaNamesOfTheWrongFormat()
    {
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
            $this->assertFalse(!(bool)Account::usernameIsValid($name));
        }
    }
}
