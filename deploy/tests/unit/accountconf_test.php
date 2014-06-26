<?php
require_once(substr(dirname(__FILE__), 0, strpos(dirname(__FILE__), 'ninjawars')+10).'deploy/resources.php');
// Core may be autoprepended in ninjawars
require_once(LIB_ROOT.'base.inc.php');
require_once(LIB_ROOT.'cleanup.inc.php'); // Profiling code at the moment.

require_once(LIB_ROOT.'control/lib_auth.php');
require_once(LIB_ROOT.'control/lib_accounts.php');

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
	public $previous_server_ip = null;

	/**
	 * group accountconf
	**/
    function purge_test_accounts($test=null){
    	TestAccountCreateAndDestroy::purge_test_accounts($test);
    	// Reusable static lib for use with various tests.
    }

	/**
	 * group accountconf
	**/
	function setUp(){
		$this->previous_server_ip = @$_SERVER['REMOTE_ADDR'];
		$_SERVER['REMOTE_ADDR']='127.0.0.1';
		$this->test_email = TestAccountCreateAndDestroy::$test_email;
		$this->test_password = TestAccountCreateAndDestroy::$test_password;
		$this->test_ninja_name = TestAccountCreateAndDestroy::$test_ninja_name;
		TestAccountCreateAndDestroy::create_testing_account();
	}
	
	/**
	 * group accountconf
	**/
	function tearDown(){
		// Delete test user.
		$this->purge_test_accounts($this->test_ninja_name);
		$_SERVER['REMOTE_ADDR']=$this->previous_server_ip; // Reset remote addr to whatever it was before, just in case.
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
        $char_id = ninja_id($this->test_ninja_name);
		$res = @login_user($this->test_email, $this->test_password);
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
		$res = authenticate($this->test_ninja_name, $this->test_password, $limit_login_times=false);
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
		$res = @login_user($this->test_ninja_name, $this->test_password);
		$this->assertTrue($confirm_worked);
		$this->assertTrue($res['success'], 'Login by ninja name failed for ['.$this->test_ninja_name.'] with password ['.$this->test_password.'] with login error: ['.$res['login_error'].']');
		$this->assertFalse((bool)$res['login_error']);
	}
	
	/**
	 * group accountconf
	**/
	function testLoginConfirmedAccountByEmail(){
		$confirm_worked = confirm_player($this->test_ninja_name, false, true); // name, no confirm #, just autoconfirm.
		$res = @login_user($this->test_email, $this->test_password);
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
		$res = @login_user($this->test_email, $this->test_password);
		$this->assertTrue($confirm_worked);
		$this->assertTrue($res['success'], 'Faded-to-inactive player unable to login');
        $this->assertFalse((bool)$res['login_error']);
	}
	
	/**
	 * group accountconf
	**/
	function testPauseAccountAndLoginShouldFail(){
		$confirm_worked = confirm_player($this->test_ninja_name, false, true); // name, no confirm #, just autoconfirm.
		$this->assertTrue((bool)$confirm_worked);
		$char_id = get_char_id($this->test_ninja_name);
		$paused = @pauseAccount($char_id); // Fully pause the account, make the operational bit = false
		$this->assertTrue((bool)$paused);
		$account_operational = query_item('select operational from accounts 
				join account_players on account_id = _account_id where _player_id = :char_id',
				array(':char_id'=>$char_id));
		$this->assertFalse($account_operational);
		$res = @login_user($this->test_email, $this->test_password);
		$this->assertFalse($res['success'], 'Login should not be successful when account is paused');
        $this->assertTrue(is_string($res['login_error']));
        $this->assertTrue((bool)$res['login_error']);
	}
	
	// Test that ninja inactivation should make them not-attackable.
}

