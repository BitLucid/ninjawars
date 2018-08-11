<?php
use NinjaWars\core\control\PasswordController;
use NinjaWars\core\data\PasswordResetRequest;
use NinjaWars\core\data\Account;
use NinjaWars\core\data\Crypto;
use NinjaWars\core\environment\RequestWrapper;
use NinjaWars\core\extensions\StreamedViewResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

class PasswordControllerTest extends NWTest {
    public function setUp() {
        parent::setUp();
        $this->account_id = TestAccountCreateAndDestroy::account_id();
        $this->account = Account::findById($this->account_id);
        $this->nonce = Crypto::nonce();
    }

    public function tearDown() {
        query("delete from password_reset_requests where nonce = '777777' or nonce = :nonce", [':nonce'=>$this->nonce]);
        TestAccountCreateAndDestroy::purge_test_accounts();
        parent::tearDown();
    }

    private function checkTestPasswordMatches($pass) {
        $phash = query_item('select phash from accounts where account_id = :id', [':id'=>$this->account_id]);
        return password_verify($pass, $phash);
    }

    public function testRequestFormRenders() {
        // Specify email request
        $req = Request::create('/password/');
        RequestWrapper::inject($req);

        // Get a Response
        $controller = new PasswordController();
        $response = $controller->index($this->m_dependencies);
        $reflection = new \ReflectionProperty(get_class($response), 'template');
        $reflection->setAccessible(true);
        $response_template = $reflection->getValue($response);
        $this->assertEquals('reset.password.request.tpl', $response_template);
    }

    public function testRequestFormRendersEvenIfLoggedOut() {
        // Specify email request
        $req = Request::create('/password/');
        RequestWrapper::inject($req);

        // Get a Response
        $controller = new PasswordController();
        $response = $controller->index($this->mockLogout());
        $reflection = new \ReflectionProperty(get_class($response), 'template');
        $reflection->setAccessible(true);
        $response_template = $reflection->getValue($response);
        $this->assertEquals('reset.password.request.tpl', $response_template);
    }

    public function testPostEmailCreatesAPasswordResetRequest() {
        // Craft Post Symfony Request
        $req = Request::create('/password/post_email/');
        $req->setMethod('POST');
        $req->request->set('email', $this->account->getActiveEmail());
        RequestWrapper::inject($req);

        // Pass to controller
        $controller = new PasswordController();
        $controller->postEmail($this->m_dependencies);

        // reset entry should be created
        $pwrr = PasswordResetRequest::where('_account_id', '=', $this->account->id())->first();

        $this->assertNotEmpty($pwrr, 'Fail: Unable to find a matching password reset request for account_id: ['.$this->account->id().'].');
        $this->assertInstanceOf(PasswordResetRequest::class, $pwrr, "Request wasn't found to become a PasswordResetRequest.");
        $this->assertGreaterThan(0, $pwrr->id());
        $this->assertNotEmpty($pwrr->nonce, "Nonce/Token was blank or didn't come back.");
        $this->nonce = $pwrr->nonce;
    }

    public function testPostEmailReturnsErrorWhenNoEmailOrNinjaName(){
        $req = Request::create('/password/post_email/');
        $req->setMethod('POST');
        RequestWrapper::inject($req);

        $controller = new PasswordController();
        $response = $controller->postEmail($this->m_dependencies);
        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertTrue(strpos($response->getTargetUrl(), rawurlencode('email or a ninja name')) !== false, 'Url Redirection did not contain expected error string');
    }

    public function testPostEmailReturnsErrorOnUnmatchableEmailAndNinjaName(){
        $req = Request::create('/password/post_email');
        $req->setMethod('POST');
        $req->request->set('email', 'unmatchable@'.Crypto::nonce().'com');
        $req->request->set('ninja_name', 'nomatch'.Crypto::nonce());
        RequestWrapper::inject($req);

        $controller = new PasswordController();
        $response = $controller->postEmail($this->m_dependencies);
        $this->assertInstanceOf(RedirectResponse::class, $response);
        $expected = 'unable to find a matching account';
        $this->assertTrue(stripos($response->getTargetUrl(), rawurlencode($expected)) !== false, 'Url Redirection for ['.$response->getTargetUrl().'] did not contain expected error string of ['.$expected.']');
    }

    public function testPostEmailCanGetAnAccountUsingANinjaName(){
        $req = Request::create('/password/post_email/');
        $req->setMethod('POST');
        $char = TestAccountCreateAndDestroy::char();
        $ninja_name = $char->name();
        $req->request->set('ninja_name', $ninja_name);
        RequestWrapper::inject($req);

        $account = Account::findByNinjaName($ninja_name);
        $this->assertNotEmpty($account->id(), 'Unable to find id for newly created account.');


        $controller = new PasswordController();
        $controller->postEmail($this->m_dependencies);
        // Check for a matching request for the appropriate account.
        $pwrr = PasswordResetRequest::where('_account_id', '=', $account->id())->first();

        $this->assertNotEmpty($pwrr, 'Fail: Unable to find a matching password reset request  for account_id: ['.$this->account->id().'].');
        $this->nonce = $pwrr->nonce;
    }

    public function testGetResetWithARandomTokenErrorRedirects(){
        $this->nonce = $token = 'asdlfkjjklkasdfjkl';

        // Symfony Request
        $request = Request::create('/password/get_reset/');
        $request->setMethod('POST');
        $request->query->set('token', $token);
        RequestWrapper::inject($request);

        // get a response
        $controller = new PasswordController();
        $response = $controller->getReset($this->m_dependencies);

        // Response should contain an array with the token in the parts.
        $this->assertInstanceOf(RedirectResponse::class, $response, 'Error! getReset matched a garbage token!');
    }

    public function testGetResetWithAValidTokenDisplaysAFilledInPasswordResetForm() {
        $token = $this->nonce = '4447744';

        // Generate a password reset req to be matched!
        PasswordResetRequest::generate($this->account, $token);
        $matched_req = PasswordResetRequest::match($token);
        $this->assertNotEmpty($matched_req);

        // Symfony Request
        $request = Request::create('/password/get_reset/');
        $request->setMethod('POST');
        $request->query->set('token', $token);
        RequestWrapper::inject($request);

        // get a response
        $controller = new PasswordController();
        $response = $controller->getReset($this->m_dependencies);

        // Response should contain an array with the token in the parts.
        $this->assertFalse($response instanceof RedirectResponse, 'Redirection to the url ['.($response instanceof RedirectResponse? $response->getTargetUrl() : null).'] was the invalid result of password reset.');

        $this->assertInstanceOf(StreamedViewResponse::class, $response, 'Response was not a StreamedViewResponse');
        $reflection = new \ReflectionProperty(get_class($response), 'data');
        $reflection->setAccessible(true);
        $response_data = $reflection->getValue($response);
        $this->assertNotEmpty($response_data);
        $this->assertEquals($response_data['token'], $token);
    }

    public function testPostResetYeildsARedirectAndAChangedPassword() {
        $this->nonce = $token = '444555666';

        // Generate a password reset req to be matched!
        PasswordResetRequest::generate($this->account, $token);

        // Create a symfony post with the right info
        // and with the token already in the database.

        // Symfony Request
        $request = Request::create('/password/post_reset/');
        $request->setMethod('POST');
        $request->request->set('token', $token);

        $password = 'new_temp_password';

        $request->request->set('new_password', $password);
        $request->request->set('password_confirmation', $password);
        $request->request->set('email', $this->account->getActiveEmail());

        RequestWrapper::inject($request);

        // Now run the controller method to reset!
        $controller = new PasswordController();
        $response = $controller->postReset($this->m_dependencies);

        // Response should be a successful redirect
        $this->assertInstanceOf(RedirectResponse::class, $response, 'Successful redirect after password resetting was not triggered!');
        $this->assertTrue(stripos($response->getTargetUrl(), 'message=Password') !== false, 'Url was ['.$response->getTargetUrl().'] instead of expected message=Password url.');

        // Password should be changed.
        $this->assertTrue($this->checkTestPasswordMatches($password), 'Password was not changed!');
    }

    public function testPostResetWithBadPasswordYeildsAnError() {
        $this->nonce = $token = '444555666';

        // Generate a password reset req to be matched!
        PasswordResetRequest::generate($this->account, $token);

        // Create a symfony post with the right info
        // and with the token already in the database.

        // Symfony Request
        $request = Request::create('/password/post_reset/');
        $request->setMethod('POST');
        $request->request->set('token', $token);

        $password = 'sh'; // Too short of a password!

        $request->request->set('new_password', $password);
        $request->request->set('password_confirmation', $password);
        $request->request->set('email', $this->account->getActiveEmail());
        RequestWrapper::inject($request);

        // Now run the controller method to reset!
        $controller = new PasswordController();
        $response = $controller->postReset($this->m_dependencies);

        $this->assertTrue(stripos($response->getTargetUrl(), rawurlencode('not long enough')) !== false, 'Url was ['.$response->getTargetUrl().'] instead of expected not long enough password error url.');

        // Password should be changed.
        $this->assertFalse($this->checkTestPasswordMatches($password), 'Password should not have been changed on a rejection!');
    }

    public function testPostResetWithMismatchedPasswordsYeildsError() {
        $this->nonce = $token = '34838383838';

        // Generate a password reset req to be matched!
        PasswordResetRequest::generate($this->account, $token);

        // Create a symfony post with the right info
        // and with the token already in the database.

        // Symfony Request
        $request = Request::create('/password/post_reset/');
        $request->setMethod('POST');
        $request->request->set('token', $token);

        $password = 'legit_password_yo';
        $request->request->set('new_password', $password);
        $request->request->set('password_confirmation', $password.'mismatch');
        $request->request->set('email', $this->account->getActiveEmail());
        RequestWrapper::inject($request);

        // Now run the controller method to reset!
        $controller = new PasswordController();
        $response = $controller->postReset($this->m_dependencies);

        $this->assertTrue(stripos($response->getTargetUrl(), rawurlencode('Password Confirmation did not match')) !== false, 'Url was ['.$response->getTargetUrl().'] instead of expected not long enough password error url.');

        // Password should be changed.
        $this->assertFalse($this->checkTestPasswordMatches($password), 'Password should not have been changed on a rejection!');
    }

    public function testPostResetWithNoTokenYeildsAnError() {
        $token = null;

        // Generate a password reset req to be matched!
        PasswordResetRequest::generate($this->account, $this->nonce);

        // Create a symfony post with the right info
        // and with the token already in the database.

        // Symfony Request
        $request = Request::create('/password/post_reset/');
        $request->setMethod('POST');
        $request->request->set('token', $token);

        $password = 'some_new_pass';

        $request->request->set('new_password', $password);
        $request->request->set('password_confirmation', $password);
        $request->request->set('email', $this->account->getActiveEmail());
        RequestWrapper::inject($request);

        // Now run the controller method to reset!
        $controller = new PasswordController();
        $response = $controller->postReset($this->m_dependencies);

        $this->assertTrue(stripos($response->getTargetUrl(), rawurlencode('No Valid')) !== false, 'Url was ['.$response->getTargetUrl().'] instead of expected not long enough password error url.');

        // Password should be changed.
        $this->assertFalse($this->checkTestPasswordMatches($password), 'Password should not have been changed on a rejection!');
    }

    public function testPostResetWithInvalidatedTokenYeildsError() {
        $this->nonce = $token = '34838383838';
        PasswordResetRequest::generate($this->account, $token);
        $request = Request::create('/password/post_reset/');
        $request->setMethod('POST');
        $request->request->set('token', $token);
        $password = 'legit_password_yo';
        $request->request->set('new_password', $password);
        $request->request->set('password_confirmation', $password);
        $request->request->set('email', $this->account->getActiveEmail());
        RequestWrapper::inject($request);

        // Invalidate the token
        PasswordResetRequest::where('_account_id', '=', $this->account->id())->update(['used' => true]);

        // Now run the controller method to reset!
        $controller = new PasswordController();
        $response = $controller->postReset($this->m_dependencies);

        $this->assertTrue(stripos($response->getTargetUrl(), rawurlencode('Token was invalid')) !== false, 'Url was ['.$response->getTargetUrl().'] instead of expected not long enough password error url.');

        // Password should be changed.
        $this->assertFalse($this->checkTestPasswordMatches($password), 'Password should not have been changed on a rejection!');
    }
}
