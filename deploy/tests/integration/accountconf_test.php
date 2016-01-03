<?php
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use NinjaWars\core\control\AccountController;

require_once(LIB_ROOT.'control/lib_auth.php');
require_once(LIB_ROOT.'control/lib_accounts.php');
require_once(ROOT.'tests/TestAccountCreateAndDestroy.php');

// Note that this file has to have a suffix of ...test.php to be run.


/* Account behavior

When an account is created, it is initially unconfirmed, but "operational" (only used to delete unwanted accounts, spammers, etc)
Login attempts when unconfirmed ask for confirmation.
After confirmation, login is allowed.
A ninja may start out as non-active, which means that they won't show up in the list until they log in.
If they are inactive for long enough, they'll be deactivated, and won't show up in the list until they log in again, at which point
their status will be toggled to "active".

A player will never have to confirm themselves more than once.
Login is all it takes to turn a ninja from "inactive" to "active", and it should happen transparently upon login.
Login should also update "last logged in" data, but that's probably better in a different test suite.

*/


/* Tests:
// create test account & player, they should start off unconfirmed, operational.
// Try to login with a newly created player, the account should not be able to be logged in (for most emails)
// confirm an account, test that the database data is toggled
// login with confirmed account, login should proceed correctly
// create a new account, confirm it, and then set it inactive.  Then perform a login.  test that after login, the ninja is toggled to active.

*/


class TestAccountConfirmation extends PHPUnit_Framework_TestCase {
	// These will be initialized in the test setup.
	public $test_email = null;
	public $test_password = null;
	public $test_ninja_name = null;

	/**
	 * group accountconf
	**/
	function setUp(){
		$_SERVER['REMOTE_ADDR']=isset($_SERVER['REMOTE_ADDR'])? $_SERVER['REMOTE_ADDR'] : '127.0.0.1';
		$this->test_email = TestAccountCreateAndDestroy::$test_email; // Something@example.com probably
		$this->test_password = TestAccountCreateAndDestroy::$test_password;
		$this->test_ninja_name = TestAccountCreateAndDestroy::$test_ninja_name;
		TestAccountCreateAndDestroy::purge_test_accounts($this->test_ninja_name);
		TestAccountCreateAndDestroy::create_testing_account();
		nw\SessionFactory::init(new MockArraySessionStorage());
	}
	
	/**
	 * group accountconf
	**/
	function tearDown(){
		// Delete test user.
		TestAccountCreateAndDestroy::purge_test_accounts($this->test_ninja_name);
    }

    function testForNinjaNameValidationErrors(){
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
	**/
	function testForNinjaThatAccountConfirmationProcessAllowsNinjaNamesOfTheRightFormat(){
		/*
		 * Username requirements (from the username_is_valid() function)
		 * A username must start with a lower-case or upper-case letter
		 * A username can contain only letters, numbers, underscores, or dashes.
		 * A username must be from 3 to 24 characters long
		 * A username cannot end in an underscore
		 * A username cannot contain 2 consecutive special characters
		 */
		$this->assertTrue((bool)username_is_valid('tchalvak'), 'Standard all alpha name tchalvak was rejected'); // This one had better be acceptable
		$this->assertTrue((bool)username_is_valid('Beagle'));
		$this->assertTrue((bool)username_is_valid('Kzqai')); // This one had better be acceptable

		$acceptable_names = array('xaz', 'NameWillBeExactly24Lett', 'tchalvak', 'Kzqai', 'Kakashi66', 'name_withunderscore', 'name-withdash',
			'ninjamaster331', 'Over_Medicated', 'No_One_Important', 'murmkuma', 'XtoxxictantrumX', 'dragon39540lkjhgfdsa', 'SasukeMoNo31',
			'SASAGAKURE', 'TheBlackPhynix', 'NGkillerdrillNG', 'BOTDFLUVER22', 'TheStripedShirtSlasher', 'sadasdasdasd124123l', 'L4RR3s222',
			'Dark-Red-EyeZ');
		foreach($acceptable_names as $name){
			$error = username_format_validate($name);
			$this->assertTrue((bool)username_is_valid($name), 'Rejected name was: ['.$name.'] and error was ['.$error.']');
		}
	}

	/**
	 * group accountconf
	**/
    function testThatTestAccountLibActuallyWorksToCreateAndDestroyATestNinja(){
    	TestAccountCreateAndDestroy::purge_test_accounts();
    	$test_char_id = TestAccountCreateAndDestroy::create_testing_account();
    	$this->assertTrue((bool)positive_int($test_char_id));
    }

	/**
	 * group accountconf
	**/
    function testCreateFullAccountConfirmAndReturnAccountId(){
    	$account_id = TestAccountCreateAndDestroy::create_complete_test_account_and_return_id();
    	$this->assertTrue((bool)positive_int($account_id));
    }

	/**
	 * group accountconf
	**/
    function testGetNinjaByName(){
        $ninja_id = ninja_id($this->test_ninja_name);
        $char_id = get_char_id($this->test_ninja_name);
        $account_id = account_id_by_ninja_id(ninja_id($this->test_ninja_name));
        $this->assertTrue(positive_int($ninja_id)>0);
        $this->assertTrue(positive_int($char_id)>0);
        $this->assertTrue($ninja_id == $char_id);
        $this->assertTrue($account_id>0);
        $this->assertTrue(positive_int($account_id)>0);
    }
	
	/**
	 * group accountconf
	**/
    function testMakeSureThatNinjaAccountIsOperationalByDefault(){
        $ninja_id = ninja_id($this->test_ninja_name);
        $this->assertTrue(positive_int($ninja_id)>0);
        $char_id = get_char_id($this->test_ninja_name);
        $this->assertTrue(positive_int($char_id)>0);
        $account_id = account_id_by_ninja_id(ninja_id($this->test_ninja_name));
        $account_operational = query_item('select operational from accounts join account_players on account_id = _account_id
        		where _player_id = :char_id', array(':char_id'=>$char_id));
        $this->assertTrue($ninja_id == $char_id);
        $this->assertTrue($account_id>0);
        $this->assertTrue(positive_int($account_id)>0);
        $this->assertTrue($account_operational, 'Account is not being set as operational by default when created');
    }

	/**
	 * group accountconf
	**/
    function testAttemptLoginOfUnconfirmedAccountShouldFail(){
    	$email ='noautoconfirm@hotmail.com'; // Create a non-autoconfirmed user
    	TestAccountCreateAndDestroy::create_testing_account(false, $email);
		$res = login_user($email, $this->test_password);
		$this->assertFalse($res['success']);
		$this->assertTrue(is_string($res['login_error']));
        $this->assertTrue((bool)$res['login_error'], 'No error returned: '.$res['login_error']);
	}
	
	/**
	 * group accountconf
	**/
	function testConfirmAccount(){
		$confirmed = confirm_player($this->test_ninja_name, false, true); // name, no confirm #, just autoconfirm.
		$active_string = query_item('select active from accounts join account_players on account_id = _account_id 
				join players on player_id = _player_id where players.uname = :uname',
				array(':uname'=>$this->test_ninja_name));
		$this->assertTrue($confirmed);
		$this->assertEquals('1', $active_string);
	}
	
	/**
	 * group accountconf
	**/
	function testConfirmPlayerAccountExists(){
        confirm_player($this->test_ninja_name, false, true); // name, no confirm #, just autoconfirm.
        $char_id = get_char_id($this->test_ninja_name);
        $this->assertTrue((bool)$char_id, 'Confirmed player creates a character id');
	}

	/**
	 * group accountconf
	**/
	function testAuthenticateConfirmedAccountByName(){
		$confirm_worked = confirm_player($this->test_ninja_name, false, true); // name, no confirm #, just autoconfirm.
		$res = authenticate($this->test_ninja_name, $this->test_password, false);
		$this->assertTrue($confirm_worked);
		$this->assertNotEmpty($res); // Should return account_id, 
		$this->assertNotEmpty($res['account_id']);
		$this->assertNotEmpty($res['account_identity']);
		$this->assertNotEmpty($res['uname']);
		$this->assertNotEmpty($res['player_id']);
	}
	
	/**
	 * group accountconf
	**/
	function testLoginConfirmedAccountByName(){
		$confirm_worked = confirm_player($this->test_ninja_name, false, true); // name, no confirm #, just autoconfirm.
		$res = login_user($this->test_ninja_name, $this->test_password);
		$this->assertTrue($confirm_worked);
		$this->assertTrue($res['success'], 'Login by ninja name failed for ['.$this->test_ninja_name.'] with password ['.$this->test_password.'] with login error: ['.$res['login_error'].']');
		$this->assertFalse((bool)$res['login_error']);
	}
	
	/**
	 * group accountconf
	**/
	function testLoginConfirmedAccountByEmail(){
		$confirm_worked = confirm_player($this->test_ninja_name, false, true); // name, no confirm #, just autoconfirm.
		$res = login_user($this->test_email, $this->test_password);
		$this->assertTrue($confirm_worked);
		$this->assertTrue($res['success'], 'Login by email failed for confirmed player ['.$this->test_ninja_name.'] with password ['.$this->test_password.'] with login error: ['.$res['login_error'].']');
		$this->assertFalse((bool)$res['login_error']);
    }

	/**
	 * group accountconf
	**/
	function testLoginConfirmedAccountWithInactivePlayerSucceeds(){
		$confirm_worked = confirm_player($this->test_ninja_name, false, true); // name, no confirm #, just autoconfirm.
		inactivate_ninja(get_char_id($this->test_ninja_name)); // Just make them fade off the active lists, inactive, don't pause the account
		$res = login_user($this->test_email, $this->test_password);
		$this->assertTrue($confirm_worked);
		$this->assertTrue($res['success'], 'Faded-to-inactive player unable to login');
        $this->assertFalse((bool)$res['login_error']);
	}
	
	/**
	 * group accountconf
	**/
	function testPauseAccountAndLoginShouldFail(){
		$accountController = new AccountController();
		$confirm_worked = confirm_player($this->test_ninja_name, false, true); // name, no confirm #, just autoconfirm.
		$this->assertTrue((bool)$confirm_worked);
		$char_id = get_char_id($this->test_ninja_name);
		$paused = $accountController->pauseAccount($char_id); // Fully pause the account, make the operational bit = false
		$this->assertTrue((bool)$paused);
		$account_operational = query_item('select operational from accounts 
				join account_players on account_id = _account_id where _player_id = :char_id',
				array(':char_id'=>$char_id));
		$this->assertFalse($account_operational);
		$res = login_user($this->test_email, $this->test_password);
		$this->assertFalse($res['success'], 'Login should not be successful when account is paused');
        $this->assertTrue(is_string($res['login_error']));
        $this->assertTrue((bool)$res['login_error']);
	}
	
	// Test that ninja inactivation should make them not-attackable.

	/**
	 * group accountconf
	**/
	function testPreconfirmEmailsReturnRightResultForGmailHotmailAndWildcardEmails(){
		$preconfirm_emails = array('test@gmail.com', 'test@example.com', 'test@russia.com');
		$no_preconfirm_emails = array('test@hotmail.com', "O'brian@yahoo.com");
		foreach($preconfirm_emails as $email){
			$this->assertTrue((bool)preconfirm_some_emails($email));
		}
		foreach($no_preconfirm_emails as $email){
			$this->assertFalse((bool)preconfirm_some_emails($email));
		}
	}

	// Test that ninja allowed names match and don't match for the choices set



	/**
	 * group accountconf
	**/
	function testThatAccountConfirmationProcessRejectsNinjaNamesOfTheWrongFormat(){
		// Same requirements as above, here we test that bad names are rejected.
		$bad_names = array('xz', 'bo', '69numfirst', 'underscorelast_', 'specialChar##', '@!#$#$^#$@#', 'double__underscore', 'double--dash');
		foreach($bad_names as $name){
			$this->assertFalse((bool)username_is_valid($name));
		}
	}

}

