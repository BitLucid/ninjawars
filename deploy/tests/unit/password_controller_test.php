<?php
// Note that the file has to have a file ending of ...test.php to be run by phpunit
require_once(CORE.'data/PasswordResetRequest.php');
require_once(CORE.'control/PasswordController.php');
require_once(ROOT.'tests/TestAccountCreateAndDestroy.php');

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class TestPasswordController extends PHPUnit_Framework_TestCase {

	function setUp(){
        $this->account_id = TestAccountCreateAndDestroy::account_id();
        assert($this->account_id>0);
        $this->account_info = account_info($this->account_id);
        $this->ninja_info = new Player(TestAccountCreateAndDestroy::char_id());
	}
	
	function tearDown(){
        TestAccountCreateAndDestroy::purge_test_accounts();
        query("delete from password_reset_requests where nonce = '777777'");
    }

    public function testRedirectPathReturnsAPath(){
        // Redirect path should return a relative-to-root path
        $controller = new PasswordController();
        $path = $controller->redirectPath();
        $this->assertNotEmpty($path);
    }

    public function testGetEmailDisplaysAFormToBeAbleToEmailAPasswordReset(){
        // Specify email request
        $req = Request::create('/passwordreset');
        // Get a Response
        $controller = new PasswordController();
        $path = $controller->getEmail($req);
        // Response should contain a form
    }

    public function testPostEmailCreatesAPasswordResetRequest(){
        // Craft Post Request
        $req = Request::create('/passwordreset');
        $req->setMethod('POST');
        $req->query->set('token', $token='666666');
        $req->query->set('email', $this->account_info['active_email']);
        // Pass to controller
        $controller = new PasswordController();
        $controller->postEmail($req);
        // reset entry should be created
        $this->assertTrue(PasswordResetRequest::match($token));
    }

    public function testGetResetWithATokenDisplaysAFilledInPasswordResetForm(){
        // specify request with token
        $req = Request::create('/passwordreset');
        $req->query->set('token', '444444');
        // get a response
        $controller = new PasswordController();
        $response = $controller->getReset($req);
        // Response should contain a form
        $content = $response->getContent();
        $this->assertTrue(strpos($content, '444444') !== false);
    }

    public function testPostResetYeildsARedirectAndAChangedPassword(){
        // craft post request with account_id, token, and password
        $req = Request::create('/passwordreset');
        $req->setMethod('POST');
        $req->query->set('token', '666666');
        $req->query->set('account_id', '666666');
        $req->query->set('password', 'fuNnewPasswordTime432');
        $controller = new PasswordController();
        $response = $controller->postReset();
        // get a response, response should be a redirect
        // password should be changed
        $this->markTestIncomplete();
    }



}

