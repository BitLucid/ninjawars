<?php
namespace NinjaWars\test;
use NinjaWars\core\data\Account;
use NinjaWars\core\control\LoginController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use NinjaWars\core\environment\RequestWrapper;
use NinjaWars\core\extensions\SessionFactory;
use \TestAccountCreateAndDestroy;

class LoginControllerTest extends \PHPUnit_Framework_TestCase {
    function setUp() {
        // Mock the post request.
        $request = new Request([], []);
        RequestWrapper::inject($request);
        SessionFactory::init(new MockArraySessionStorage());
    }

    function tearDown() {
        RequestWrapper::inject(new Request([]));
        $session = SessionFactory::getSession();
        $session->invalidate();
        TestAccountCreateAndDestroy::destroy();
    }

    public function testLoginControllerCanBeInstantiatedWithoutError() {
        $controller = new LoginController();
        $this->assertInstanceOf('NinjaWars\core\control\LoginController', $controller);
    }

    public function testLoginWithGibberishFails() {
        $controller = new LoginController();
        $error_message = $controller->performLogin('gibber', 'ish');
        $this->assertNotEmpty($error_message);
    }

    public function testLoginIndexShouldDisplay(){
        $controller = new LoginController();
        $res = $controller->index();
        $this->assertFalse($res['parts']['authenticated']);
    }

    public function testShouldRedirectIfAuthenticated(){
        $session = SessionFactory::getSession();
        $session->set('authenticated', true);
        $controller = new LoginController();
        $res = $controller->index();
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\RedirectResponse', $res);
    }

    public function testLoginRequestWithBlanksShouldError(){
        $controller = new LoginController();
        $res = $controller->requestLogin();
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\RedirectResponse', $res);
        $this->assertTrue(stripos($res->getTargetUrl(), 'error') !== false);
    }

    public function testLoginRequestWithBadFilledValuesShouldError(){
        $request = new Request([], ['user'=>'bob', 'pass'=>'james']);
        RequestWrapper::inject($request);
        $controller = new LoginController();
        $res = $controller->requestLogin();
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\RedirectResponse', $res);
        $this->assertTrue(stripos($res->getTargetUrl(), 'error') !== false);
    }

    public function testLoginRequestWithNewlyCreatedAccountShouldWork(){
        $account = Account::findById(TestAccountCreateAndDestroy::account_id());
        $this->assertInstanceOf('NinjaWars\core\data\Account', $account);
        $request = new Request([], ['user'=>$account->account_identity, 'pass'=>TestAccountCreateAndDestroy::$test_password]);
        // TestAccountCreateAndDestroy::$test_password
        RequestWrapper::inject($request);
        $controller = new LoginController();
        $res = $controller->requestLogin();
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\RedirectResponse', $res);
        $this->assertTrue(stripos($res->getTargetUrl(), 'error') === false);
    }

    public function testUnconfirmedAccountShouldProduceLoginError(){
        $account = Account::findById(TestAccountCreateAndDestroy::account_id());
        $this->assertInstanceOf('NinjaWars\core\data\Account', $account);
        $account->confirmed = 0;
        $account->save();
        $request = new Request([], ['user'=>$account->account_identity, 'pass'=>TestAccountCreateAndDestroy::$test_password]);
        // TestAccountCreateAndDestroy::$test_password
        RequestWrapper::inject($request);
        $controller = new LoginController();
        $res = $controller->requestLogin();
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\RedirectResponse', $res);
        $this->assertTrue(stripos($res->getTargetUrl(), 'error') !== false);
    }

    public function testLoginShouldFailOnBlanks(){
        $account = Account::findById(TestAccountCreateAndDestroy::account_id());
        $this->assertInstanceOf('NinjaWars\core\data\Account', $account);
        $account->confirmed = 0;
        $account->save();
        $request = new Request([], ['user'=>'', 'pass'=>'']);
        // TestAccountCreateAndDestroy::$test_password
        RequestWrapper::inject($request);
        $controller = new LoginController();
        $res = $controller->requestLogin();
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\RedirectResponse', $res);
        $this->assertTrue(stripos($res->getTargetUrl(), 'error') !== false);
    }

    public function testStorageOfAuthAttemptShouldNotError(){
        $request = RequestWrapper::$request;
        $attempt_info = [
            'username'        => 'james',
            'user_agent'      => 'phpunit cli',
            'ip'              => $request->getClientIp(),
            'successful'      => 0,
            'additional_info' => []
        ];
        LoginController::store_auth_attempt($attempt_info);
        $this->assertTrue(true); // Just check that no error occurs before this point
    }
}
