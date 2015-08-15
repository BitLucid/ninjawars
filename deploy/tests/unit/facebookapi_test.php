<?php
// Core may be autoprepended in ninjawars
require_once(LIB_ROOT.'base.inc.php');
require_once(ROOT.'tests/TestAccountCreateAndDestroy.php');
require_once(ROOT.'core/data/AccountFactory.php');
require_once(ROOT.'core/data/Account.php');

// Note that the file has to have a file ending of ...test.php to be run by phpunit


class TestFacebookAPI extends PHPUnit_Framework_TestCase {

	var $facebookAccountId = 8889999777;
	var $accountId;


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
		$account_id = TestAccountCreateAndDestroy::create_complete_test_account_and_return_id();
		$this->accountId = $account_id;
	}
	
	/**
	 * group facebookapi
	**/
	function tearDown(){
		session_destroy();
		TestAccountCreateAndDestroy::purge_test_accounts();
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
    function testFindAccountByStaticFacebookOauthId(){
    	$fake_fb_id = 44994455666;
    	$account_id = TestAccountCreateAndDestroy::create_complete_test_account_and_return_id();
    	$account = new Account($account_id);
    	$account->setOauthId($fake_fb_id);
    	$account->setOauthProvider('facebook');
    	$was_updated = AccountFactory::save($account);
    	$this->assertGreaterThan(0, $was_updated);
    	$saved_account = AccountFactory::findById($account_id);
    	$this->assertGreaterThan(0, $saved_account->getOauthId());
    	$updated_account = AccountFactory::findAccountByOauthId($fake_fb_id);
    	$this->assertNotEmpty(find_account_info_by_oauth($fake_fb_id), 'Unable to find account via function oauth match');
    	$this->assertTrue($saved_account instanceof Account && (bool) $saved_account->getIdentity(), 'Test Account does not seem to be creating successfully.');
    	$this->assertTrue($updated_account instanceof Account, 'Account oauth not finding account match.');
    	$this->assertTrue((bool)$updated_account->getIdentity(), 'Updated Account saved has no valid identity.');
    	$this->assertEquals($fake_fb_id, $updated_account->getOauthId('facebook'));
    }

    function testSyncAccountToFacebookOauthId(){
    	$fb_user_id = 444446664443333;
    	$account_id = TestAccountCreateAndDestroy::create_complete_test_account_and_return_id();
    	$account = new Account($account_id);
    	$account->setOauthId($fb_user_id);
    	$account->setOauthProvider('facebook');
    	$this->assertEquals('facebook', $account->getOauthProvider());
    	AccountFactory::save($account);
    	$account_updated = AccountFactory::findAccountByOauthId($fb_user_id);
    	$this->assertEquals($fb_user_id, $account_updated->getOauthId('facebook'));
    }

	function testLoginViaOauthFailsWithRandomOauthThatWontExist(){
		$fb_user_id = 8999999994444444;
		$oauth_id = $fb_user_id;
		$logged_in_info = login_user_by_oauth($oauth_id);  // Try to login that user with the arbitrary id setting.
		$this->assertFalse($logged_in_info['success']);
    	$this->assertTrue((bool)$logged_in_info['login_error']);
	}

	function testLoginViaCorrectOauth(){
		$fb_user_id = 8999994444444;
		$account_id = TestAccountCreateAndDestroy::create_complete_test_account_and_return_id();
		$account = AccountFactory::find($account_id);
		$account->setOauthId($fb_user_id, 'facebook');
		AccountFactory::save($account);
		$oauth_id = $fb_user_id;
		$logged_in_info = login_user_by_oauth($oauth_id);
		$this->assertNull($logged_in_info['login_error']);
		$this->assertTrue($logged_in_info['success'], 'No login success indicator was found for oauth login!');
	}

    function testLoginOfCreatedTestingAccountViaMockFacebookSync(){
    	$initial_ip = @$_SERVER['REMOTE_ADDR'];
    	$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
	    // Create test user account.
	    // Connect test user account to arbitrary oauth_provider and oauth_uid
    	$fb_user_id = 77775555555555;
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

