<?php
require_once(__DIR__.'/../resources.php');
require_once(LIB_ROOT.'base.inc.php');

class TestAccountCreateAndDestroy{
	public static $test_email = 'testphpunit@example.com';
	public static $alt_test_email = 'testphpunit2@example.com';
	public static $test_password = 'password';
	public static $test_ninja_name = 'phpunit_ninja_name';
	public static $alt_test_ninja_name = 'phpunit_alt_ninja';

	// Library for creating and destroying test-only accounts, for use in their various ways in testing.
	public static function purge_test_accounts($test=null){
	    $test_ninja_name = $test? $test : TestAccountCreateAndDestroy::$test_ninja_name;
	    $active_email = TestAccountCreateAndDestroy::$test_email;
	    $alt_active_email = TestAccountCreateAndDestroy::$alt_test_email;
	    $aid = get_char_id(TestAccountCreateAndDestroy::$test_ninja_name);
	    query('delete from players where player_id in 
	        (select player_id from players left join account_players on _player_id = player_id 
	        	left join accounts on _account_id = account_id 
	            where active_email = :active_email or account_identity= :ae2 or players.uname = :uname or active_email = :alt_active_email or account_identity = :alt_active_email2)', 
	        array(':active_email'=>$active_email, ':ae2'=>$active_email, ':uname'=>$test_ninja_name, ':alt_active_email'=>$alt_active_email, ':alt_active_email2'=>$alt_active_email)); // Delete the players
	    query('delete from account_players where _account_id in (select account_id from accounts 
	            where active_email = :active_email or account_identity= :ae2 or active_email = :alt_active_email or account_identity = :alt_active_email2)', // Delete the account_players linkage.
	        array(':active_email'=>$active_email, ':ae2'=>$active_email,  ':alt_active_email'=>$alt_active_email, ':alt_active_email2'=>$alt_active_email));
	    $query = query('delete from accounts where active_email = :active_email or account_identity= :ae2 or active_email = :alt_active_email or account_identity = :alt_active_email2', 
	    	array(':active_email'=>$active_email, ':ae2'=>$active_email,  ':alt_active_email'=>$alt_active_email, ':alt_active_email2'=>$alt_active_email)); // Finally, delete the test account.
	    return ($query->rowCount() > 0);
	    
	    /*
	    For manual deletion:
	delete from players where player_id in (select player_id from players left join account_players on _player_id = player_id left join accounts on _account_id = account_id where active_email = 'testphpunit@example.com' or account_identity='testphpunit@example.com');	
	delete from account_players where _account_id in (select account_id from accounts where active_email = 'testphpunit@example.com' or account_identity='testphpunit@example.com');
	delete from accounts where active_email = 'testphpunit@example.com' or account_identity='testphpunit@example.com';
	    */
	}

	// Create a testing account
	public static function create_testing_account($confirm=false){
		@session_start();
		$ip = isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1';
		//$previous_server = @$_SERVER['REMOTE_ADDR'];
		//$_SERVER['REMOTE_ADDR']=isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR']? $_SERVER['REMOTE_ADDR'] : '127.0.0.1';
		TestAccountCreateAndDestroy::purge_test_accounts();
		$found = get_char_id(TestAccountCreateAndDestroy::$test_ninja_name);
	    if((bool)$found){
			throw new Exception('Test user found ['.$found.'] with name ['.TestAccountCreateAndDestroy::$test_ninja_name.'] already exists');
		}
		// Create test user, unconfirmed, whatever the default is for activity.
		$preconfirm = true;
		$confirm = rand(1000,9999); //generate confirmation code

		// Use the function from lib_player
		$player_params = array(
			'send_email'    => TestAccountCreateAndDestroy::$test_email
			, 'send_pass'   => TestAccountCreateAndDestroy::$test_password
			, 'send_class'  => 'dragon'
			, 'preconfirm'  => true
			, 'confirm'     => $confirm
			, 'referred_by' => 'ninjawars.net'
			, 'ip'			=> $ip
		);
		ob_start(); // Skip extra output
		$error = create_account_and_ninja(TestAccountCreateAndDestroy::$test_ninja_name, $player_params);
		ob_end_clean();
		if($confirm){
			$confirmed = confirm_player(TestAccountCreateAndDestroy::$test_ninja_name, false, true); // name, no confirm #, just autoconfirm.
		}
		//$_SERVER['REMOTE_ADDR']=$previous_server; // Reset remote addr to whatever it was before.
		$char_id = get_char_id(TestAccountCreateAndDestroy::$test_ninja_name);
		return $char_id;
	}

	// Create a separate, second testing account
	public static function create_alternate_testing_account($confirm=false){
		$ip = isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1';
		if((bool)get_char_id(TestAccountCreateAndDestroy::$alt_test_ninja_name)){
			throw new Exception('Test user found ['.$found.'] with name ['.TestAccountCreateAndDestroy::$alt_test_ninja_name.'] already exists');
		}
		// Create test user, unconfirmed, whatever the default is for activity.
		$preconfirm = true;
		$confirm = rand(1000,9999); //generate confirmation code

		// Use the function from lib_player
		$player_params = array(
			'send_email'    => TestAccountCreateAndDestroy::$alt_test_email
			, 'send_pass'   => TestAccountCreateAndDestroy::$test_password
			, 'send_class'  => 'dragon'
			, 'preconfirm'  => true
			, 'confirm'     => $confirm
			, 'referred_by' => 'ninjawars.net'
			, 'ip'			=> $ip
		);
		ob_start(); // Skip extra output
		$error = create_account_and_ninja(TestAccountCreateAndDestroy::$alt_test_ninja_name, $player_params);
		ob_end_clean();
		if($confirm){
			$confirmed = confirm_player(TestAccountCreateAndDestroy::$alt_test_ninja_name, false, true); // name, no confirm #, just autoconfirm.
		}
		$char_id = get_char_id(TestAccountCreateAndDestroy::$alt_test_ninja_name);
		return $char_id;
	}

	// Convenience wrapper for the above, but confirms the account and returns the account id.
	public static function create_complete_test_account_and_return_id(){
		$char_id = TestAccountCreateAndDestroy::create_testing_account($confirm=true);
		$account_info = account_info_by_char_id($char_id);
		return $account_info['account_id'];
	}

	// Alias for create_testing_account but clearer.
	public static function char_id($confirm=false){
		return TestAccountCreateAndDestroy::create_testing_account($confirm);
	}

	public static function char_id_2($confirm=false){
		return TestAccountCreateAndDestroy::create_alternate_testing_account($confirm);
	}

	// Alias to get an account id
	public static function account_id(){
		return TestAccountCreateAndDestroy::create_complete_test_account_and_return_id();
	}

}