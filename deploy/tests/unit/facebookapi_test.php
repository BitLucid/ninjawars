<?php
require_once(substr(dirname(__FILE__), 0, strpos(dirname(__FILE__), 'ninjawars')+10).'deploy/resources.php');
// Core may be autoprepended in ninjawars
require_once(LIB_ROOT.'base.inc.php');

// Note that the file has to have a file ending of ...test.php to be run by phpunit


class TestFacebookAPI extends PHPUnit_Framework_TestCase {


	/**
	 * group facebookapi
	**/
	function setUp(){
		require_once(ROOT.'core/control/lib_accounts.php');
		require_once(ROOT.'core/control/lib_auth.php');
		require_once(ROOT.'core/control/lib_api.php');
		if (($loader = require_once ROOT . '/vendor/autoload.php') == null)  {
		  die('Vendor directory for facebook API not found, Please run composer install.');
		}
		@session_start(); // Session won't exactly work for cli phpunit, but worth a try.

	}
	
	/**
	 * group facebookapi
	**/
	function tearDown(){
		session_destroy();
    }

	/**
	 * group facebookapi
	**/
    public function getAppID(){
    	if(!defined('FACEBOOK_APP_ID')){
    		throw new Exception('Facebook App ID for api not defined!');
    	}
    	return FACEBOOK_APP_ID; // Not secret.
    }

	/**
	 * group facebookapi
	**/
    public function getAppSecret(){
    	if(!defined('FACEBOOK_APP_SECRET')){
    		throw new Exception('Facebook App Secret for api not defined!');
    	}
    	return FACEBOOK_APP_SECRET; // Secret from: https://developers.facebook.com/apps/30479872633/dashboard/
    }

	/**
	 * group facebookapi
	**/
    function testCreatedAFacebookObject(){
		$facebook = new Facebook(array(
		  'appId'  => $this->getAppId(),
		  'secret' => $this->getAppSecret(),
		));

    	$this->assertTrue($facebook instanceof Facebook);
    }


	/**
	 * group facebookapi
	**/
    function testFailToCreateFacebookUser(){
		$facebook = new Facebook(array(
		  'appId'  => $this->getAppId(),
		  'secret' => $this->getAppSecret(),
		));

		// Get User ID
		$user = $facebook->getUser();
    	$this->assertFalse((bool)$user);
    }

    // Testing the failures above and below may be about all that can be done from a non-browser-based test.
    // Probably would be hard to test even with a browser-based test running.


	/**
	 * group facebookapi
	**/
    function testFailToCreateFacebookUserProfile(){
		$facebook = new Facebook(array(
		  'appId'  => $this->getAppId(),
		  'secret' => $this->getAppSecret(),
		));

		// Get User ID
		$user = $facebook->getUser();
		if ($user) {
			try {
			// Proceed knowing you have a logged in user who's authenticated.
				$user_profile = $facebook->api('/me');
			} catch (FacebookApiException $e) {
				echo '<pre>'.htmlspecialchars(print_r($e, true)).'</pre>';
				$user = null;
			}
		}
    	$this->assertFalse((bool)$user);
    	// Just checking
    }

    // These are the after-facebook things that are done to login a user after they've done a facebook login.

	/**
	 * group facebookapi
	**/
    function testFindAccountByStaticFacebookId(){
    	$fb_user_id = '10100268595264896';
    	$account_info = find_account_info_by_oauth($fb_user_id, $provider='facebook');
    	$this->assertNotEmpty($account_info);
    	$this->assertTrue((bool)positive_int($account_info['account_id']));
    }

	/**
	 * group facebookapi
	**/
    function testSyncAccountToFacebookOauthId(){
    	$fb_user_id = '10100268595264896';
    	$account_info = account_info_by_identity($identity_email='tchalvak@gmail.com');
    	$this->assertNotEmpty($account_info);
    	$this->assertTrue((bool)positive_int($account_info['account_id']));
    	$added = add_oauth_to_account($account_info['account_id'], $fb_user_id);
    	$this->assertTrue($added);
    }

	/**
	 * group facebookapi
	**/
    function testSyncOfSimpleForTestingAccount(){
	    // Create test user account.
	    // Connect test user account to arbitrary oauth_provider and oauth_uid
    	$fb_user_id = '10100268595264896';
    	$account_id = TestAccountCreateAndDestroy::create_complete_test_account_and_return_id();
    	$this->assertTrue((bool)positive_int($account_id));
    	$email = TestAccountCreateAndDestroy::$test_email;
    	$this->assertTrue((bool)$email);
    	$account_info = account_info_by_identity($identity_email=$email);
    	$this->assertTrue((bool)positive_int($account_info['account_id']));
    	$added = add_oauth_to_account($account_info['account_id'], $fb_user_id);
    	$this->assertTrue($added);
    }

	/**
	 * group facebookapi
	**/
	function testLoginViaOauthFailsWithRandomOauthThatWontExist(){
		$fb_user_id = '89999999999994444444';
		$oauth_id = $fb_user_id;
		$logged_in_info = login_user_by_oauth($oauth_id);  // Try to login that user with the arbitrary id setting.
		$this->assertFalse($logged_in_info['success']);
    	$this->assertTrue((bool)$logged_in_info['login_error']);
	}


	/**
	 * group facebookapi
	**/
    function testLoginOfCreatedTestingAccountViaMockFacebookSync(){
    	$initial_ip = @$_SERVER['REMOTE_ADDR'];
    	$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
	    // Create test user account.
	    // Connect test user account to arbitrary oauth_provider and oauth_uid
    	$fb_user_id = '10100268595264896';
    	$account_id = TestAccountCreateAndDestroy::create_complete_test_account_and_return_id();
    	$this->assertTrue((bool)positive_int($account_id));
    	$email = TestAccountCreateAndDestroy::$test_email;
    	$this->assertTrue((bool)$email);
    	$account_info = account_info_by_identity($identity_email=$email);
    	$this->assertTrue((bool)positive_int($account_info['account_id']));
    	$added = add_oauth_to_account($account_info['account_id'], $fb_user_id);
    	$this->assertTrue($added);
    	$account_info = account_info_by_identity($email);
    	$oauth_id = $account_info['oauth_id'];
    	$this->assertEquals($oauth_id, $fb_user_id);
    	$logged_in_info = @login_user_by_oauth($oauth_id);  // Try to login that user with the arbitrary id setting.
    	$this->assertTrue($logged_in_info['success']);
    	$this->assertFalse((bool)$logged_in_info['login_error']);
    	$_SERVER['REMOTE_ADDR'] = $initial_ip;
    }
    

    // if user is already logged in, don't reconnect them


}

