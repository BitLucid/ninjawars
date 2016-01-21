<?php
// Note that the file has to have a file ending of ...test.php to be run by phpunit
require_once(CORE.'data/PasswordResetRequest.php');
require_once(CORE.'control/PasswordController.php');
require_once(ROOT.'tests/TestAccountCreateAndDestroy.php');

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

use app\data\PasswordResetRequest;


class TestPasswordController extends PHPUnit_Framework_TestCase {

	function setUp(){
        $this->account_id = TestAccountCreateAndDestroy::account_id();
        //assert($this->account_id>0);
        $this->account = AccountFactory::findById($this->account_id);
        $this->nonce = null;
	}
	
	function tearDown(){
        query("delete from password_reset_requests where nonce = '777777' or nonce = :nonce", [':nonce'=>$this->nonce]);
        TestAccountCreateAndDestroy::purge_test_accounts();
    }

    private function checkTestPasswordMatches($pass){
        $phash = query_item('select phash from accounts where account_id = :id', [':id'=>$this->account_id]);
        return password_verify($pass, $phash);
    }

    public function testRequestFormRenders(){
        // Specify email request
        $req = Request::create('/resetpassword.php');
        /*$req->query->set('error', null);
        $req->request->set('email', null);
        $req->request->set('ninja_name', null);
        $req->query->set('message', 'Some message here');*/
        // Get a Response
        $controller = new PasswordController();
        $controller->debug_emails = false; // Don't debug emails.
        $response = $controller->getEmail($req);
        $this->assertEquals('request_password_reset.tpl', $response['template']);
    }

    public function testPostEmailCreatesAPasswordResetRequest(){
        // Craft Post Symfony Request
        $req = Request::create('/resetpassword.php');
        $req->setMethod('POST');
        $req->query->set('email', $this->account->getActiveEmail());
        // Pass to controller
        $controller = new PasswordController();
        $controller->debug_emails = false; // Don't debug emails.
        $response = $controller->postEmail($req);
        // reset entry should be created
        $req = PasswordResetRequest::where('_account_id', '=', $this->account->id())->first();
        
        $this->assertNotEmpty($req, 'No matching password reset request was found');
        $this->assertTrue($req instanceof PasswordResetRequest, "Request wasn't found to become a PasswordResetRequest.");
        $this->assertGreaterThan(0, $req->id());
        $this->assertNotEmpty($req->nonce, 'Nonce/Token was blank or didn\'t come back.');
    }

    public function testGetResetWithAValidTokenDisplaysAFilledInPasswordResetForm(){
        $token = '4447744';
        // Generate a password reset req to be matched!
        PasswordResetRequest::generate($this->account, $token);
        $matched_req = PasswordResetRequest::match($token);
        $this->assertNotEmpty($matched_req);
        // Symfony Request
        $req = Request::create('/resetpassword.php');
        $req->setMethod('POST');
        $req->query->set('token', $token);
        $req->query->set('email', $this->account->getActiveEmail());

        // get a response
        $controller = new PasswordController();
        $response = $controller->getReset($req);
        // Response should contain an array with the token in the parts.
        $this->assertFalse($response instanceof RedirectResponse, 'Redirection to the url ['.$response->getTargetUrl().'] was the invalid result of password reset.');
        $this->assertTrue(is_array($response), 'Response was not a ViewSpec Array');
        $this->assertNotEmpty($response['parts']);
        $this->assertEquals($response['parts']['token'], $token);
    }

    public function testPostResetYeildsARedirectAndAChangedPassword(){
        $token = '444555666';
        // Generate a password reset req to be matched!
        PasswordResetRequest::generate($this->account, $token);

        // Create a symfony post with the right info
        // and with the token already in the database.

        // Symfony Request
        $req = Request::create('/resetpassword.php');
        $req->setMethod('POST');
        $req->query->set('token', $token);

        $password = 'new_temp_password';

        $req->query->set('new_password', $password);
        $req->query->set('confirm_password', $password);
        $req->query->set('email', $this->account->getActiveEmail());

        // Now run the controller method to reset!
        $controller = new PasswordController();
        $response = $controller->postReset($req);

        // Response should be a successful redirect
        $this->assertTrue($response instanceof RedirectResponse, 'Successful redirect after password resetting was not triggered!');

        // Password should be changed.

        $this->assertTrue($this->checkTestPasswordMatches($password), 'Password was not changed!');
    }



}



