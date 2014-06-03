<?php
require_once(substr(dirname(__FILE__), 0, strpos(dirname(__FILE__), 'ninjawars')+10).'deploy/resources.php');
// Core may be autoprepended in ninjawars
require_once(LIB_ROOT.'base.inc.php');
require_once(LIB_ROOT.'cleanup.inc.php'); // Profiling code at the moment.

require_once(LIB_ROOT.'control/lib_auth.php');
require_once(LIB_ROOT.'control/lib_accounts.php');

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
	var $test_email = 'testphpunit@example.com';
	var $test_password = 'password';
	var $test_ninja_name = 'phpunit_ninja_name';

	/**
	 * group accountconf
	**/
    function purge_test_accounts($test=null){
        $test_ninja_name = $test? $test : 'phpunit_ninja_name';
        $active_email = 'testphpunit@example.com';
        $aid = get_char_id($test_ninja_name);
        query('delete from players where player_id in 
            (select player_id from players join account_players on _player_id = player_id 
            	join accounts on _account_id = account_id 
                where active_email = :active_email or account_identity= :ae2 or players.uname = :uname)', 
            array(':active_email'=>$active_email, ':ae2'=>$active_email, ':uname'=>$test_ninja_name)); // Delete the players
        query('delete from account_players where _account_id in (select account_id from accounts 
                where active_email = :active_email or account_identity= :ae2)', // Delete the account_players linkage.
            array(':active_email'=>$active_email, ':ae2'=>$active_email));
        query('delete from accounts where active_email = :active_email or account_identity= :ae2', 
        	array(':active_email'=>$active_email, ':ae2'=>$active_email)); // Finally, delete the test account.
        
        /*
        For manual deletion:
    delete from players where player_id in (select player_id from players left join account_players on _player_id = player_id left join accounts on _account_id = account_id where active_email = 'testphpunit@example.com' or account_identity='testphpunit@example.com');	
    delete from account_players where _account_id in (select account_id from accounts where active_email = 'testphpunit@example.com' or account_identity='testphpunit@example.com');
    delete from accounts where active_email = 'testphpunit@example.com' or account_identity='testphpunit@example.com';
        */
    }

	/**
	 * group accountconf
	**/
	function setUp(){
		$prev = error_reporting(0);
		session_start();
		error_reporting($prev);
		$_SERVER['REMOTE_ADDR']='127.0.0.1';
		$this->purge_test_accounts();
		$found = get_char_id($this->test_ninja_name);
        if($found){
			throw new Exception('Test user already exists');
		}
		// Create test user, unconfirmed, whatever the default is for activity.
		$preconfirm = true;
		$confirm = rand(1000,9999); //generate confirmation code

		// Use the function from lib_player
		$player_params = array(
			'send_email'    => $this->test_email
			, 'send_pass'   => $this->test_password
			, 'send_class'  => 'dragon'
			, 'preconfirm'  => true
			, 'confirm'     => $confirm
			, 'referred_by' => 'ninjawars.net'
		);
		ob_start(); // Skip extra output
		$error = create_account_and_ninja($this->test_ninja_name, $player_params);
		ob_end_clean();
	}
	
	/**
	 * group accountconf
	**/
	function tearDown(){
		// Delete test user.
		$this->purge_test_accounts($this->test_ninja_name);
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
		confirm_player($this->test_ninja_name, false, true); // name, no confirm #, just autoconfirm.
		pauseAccount(get_char_id($this->test_ninja_name)); // Fully pause the account, make the operational bit = false
		$res = @login_user($this->test_email, $this->test_password);
		debug($res);
		$this->assertFalse($res['success']);
        $this->assertTrue(is_string($res['login_error']));
        $this->assertTrue((bool)$res['login_error']);
	}
	
	// Test that ninja inactivation should make them not-attackable.
}

