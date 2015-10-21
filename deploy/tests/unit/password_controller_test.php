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
        $this->account = AccountFactory::findById($this->account_id);
        $this->ninja_info = new Player(TestAccountCreateAndDestroy::char_id());
	}
	
	function tearDown(){
        TestAccountCreateAndDestroy::purge_test_accounts();
        query("delete from password_reset_requests where nonce = '777777'");
    }

    private function checkTestPasswordMatches($pass){
        $phash = query_item('select phash from accounts where account_id = :id', [':id'=>$this->account_id]);
        return password_verify($pass, $phash);
    }

    public function testRedirectPathReturnsAPath(){
        // Redirect path should return a relative-to-root path
        $controller = new PasswordController();
        $path = $controller->redirectPath();
        $this->assertNotEmpty($path);
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
        $response = $controller->getRequestForm($req);
        // Response should contain a form
        $content = $response->getContent();
        $this->assertTrue(strpos($content, '<form') !== false);
        /*$this->assertTrue(strpos($content, 'Some message here') !== false);*/
    }

    public function testPostEmailCreatesAPasswordResetRequest(){
        // Craft Post Request
        $req = Request::create('/resetpassword.php');
        $req->setMethod('POST');
        $req->query->set('token', $token='666666');
        $req->query->set('email', $this->account->getActiveEmail());
        // Pass to controller
        $controller = new PasswordController();
        $controller->debug_emails = false; // Don't debug emails.
        //$this->markTestIncomplete();
        $response = $controller->postEmail($req);
        // reset entry should be created
        $data = PasswordResetRequest::match($token);
        $this->markTestIncomplete();
        $this->assertTrue($data['request_id']);
    }

    public function testGetResetWithATokenDisplaysAFilledInPasswordResetForm(){
        $token = '444444';
        // get a response
        $controller = new PasswordController();
        $response = $controller->getReset($token);
        // Response should contain a form
        $content = $response->getContent();
        $this->assertTrue(strpos($content, '444444') !== false);
    }

    public function testPostResetYeildsARedirectAndAChangedPassword(){
        $account = PasswordResetRequest::request($this->account_id);
        throw new Exception('Have to fix this more fully.');
        $pass = 'fuNnewPasswordTime432';
        // craft post request with account_id, token, and password
        $req = Request::create('/passwordreset');
        $req->setMethod('POST');
        $req->request->set('token', '666666');
        $req->request->set('password', $pass);
        // get a response, response should be a redirect
        $controller = new PasswordController();
        $response = $controller->postReset($req);
        // password should be changed
        $this->assertTrue($this->checkTestPasswordMatches($pass));
    }



}

