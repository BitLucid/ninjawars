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


    function purge_test_accounts($test=null){
        $test_ninja_name = $test? $test : 'phpunit_ninja_name';
        $active_email = 'testphpunit@example.com';
        $aid = get_char_id($test_ninja_name);
        query('delete from players where player_id in 
            (select player_id from players join account_players on _player_id = player_id join accounts on _account_id = account_id 
                where active_email = :active_email or account_identity= :ae2 or players.uname = :uname)', 
            array(':active_email'=>$active_email, ':ae2'=>$active_email, ':uname'=>$test_ninja_name)); // Delete the players
        query('delete from account_players where _account_id in (select account_id from accounts 
                where active_email = :active_email or account_identity= :ae2)', // Delete the account_players linkage.
            array(':active_email'=>$active_email, ':ae2'=>$active_email));
        query('delete from accounts where active_email = :active_email or account_identity= :ae2', array(':active_email'=>$active_email, ':ae2'=>$active_email)); // Finally, delete the test account.
        
        /*
        For manual deletion:
    delete from players where player_id in (select player_id from players left join account_players on _player_id = player_id left join accounts on _account_id = account_id where active_email = 'testphpunit@example.com' or account_identity='testphpunit@example.com');	
    delete from account_players where _account_id in (select account_id from accounts where active_email = 'testphpunit@example.com' or account_identity='testphpunit@example.com');
    delete from accounts where active_email = 'testphpunit@example.com' or account_identity='testphpunit@example.com';
        */
    }

	function setUp(){
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
		if (create_account_and_ninja($this->test_ninja_name, $player_params)) { // Create the player.
			$error = 'There was a problem with creating a player account. Please contact us as mentioned below: ';
		}
		ob_end_clean();
	}
	
	function tearDown(){
		// Delete test user.
		$this->purge_test_accounts($this->test_ninja_name);
	}
	
	function testAttemptLoginOfUnconfirmedAccountShouldFail(){
		$res = login_user($this->test_email, $this->test_password);
		$this->assertFalse($res['success']);
		$this->assertTrue($res['login_error']);
	}
	
	function testConfirmAccount(){
		$confirmed = confirm_player($this->test_ninja_name, false, true); // name, no confirm #, just autoconfirm.
		$this->t($confirmed);
	}
	
	function testLoginConfirmedAccount(){
		confirm_player($this->test_ninja_name, false, true); // name, no confirm #, just autoconfirm.
		$res = login_user($this->test_email, $this->test_password);
		$this->assertTrue($res['success']);
		$this->assertFalse($res['login_error']);
	}
	
	function testLoginConfirmedAccountWithInactivePlayerSucceeds(){
		confirm_player($this->test_ninja_name, false, true); // name, no confirm #, just autoconfirm.
		inactivate_ninja(get_char_id($this->test_ninja_name)); // Just make them fade off the active lists, inactive, don't pause the account
		$res = login_user($this->test_email, $this->test_password);
		$this->assertTrue($res['success']);
		$this->assertFalse($res['login_error']);
	}
	
	function testPauseAccountAndLoginShouldFail(){
		confirm_player($this->test_ninja_name, false, true); // name, no confirm #, just autoconfirm.
		pauseAccount(get_char_id($this->test_ninja_name)); // Fully pause the account, make the operational bit = false
		$res = login_user($this->test_email, $this->test_password);
		$this->assertFalse($res['success']);
		$this->assertTrue($res['login_error']);
	}
	
	// Test that ninja inactivation should make them not-attackable.
}

