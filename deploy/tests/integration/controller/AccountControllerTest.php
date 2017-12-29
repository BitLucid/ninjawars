<?php
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use NinjaWars\core\control\AccountController;
use NinjaWars\core\extensions\SessionFactory;
use NinjaWars\core\environment\RequestWrapper;
use NinjaWars\core\data\Player;
use NinjaWars\core\data\Account;

class AccountControllerTest extends PHPUnit_Framework_TestCase {
	public function setUp() {
		SessionFactory::init(new MockArraySessionStorage());
        $char_id = TestAccountCreateAndDestroy::char_id();
        $char = Player::find($char_id);
        $account = Account::findByChar($char);
        $account_id = $account->id();
        SessionFactory::getSession()->set('authenticated', true);
        SessionFactory::getSession()->set('player_id', $char_id);
        SessionFactory::getSession()->set('account_id', $account_id);
        $this->deps = [
            'current_player'=>$char,
            'account'=>$account,
            'account_id'=>$account_id
        ];
	}

	public function tearDown() {
        $session = SessionFactory::getSession();
        $session->invalidate();
        unset($this->deps);
    }

    public function testInstantiation() {
        $controller = new AccountController();
        $this->assertInstanceOf('NinjaWars\core\control\AccountController', $controller);
    }

    public function testIndexRuns() {
        $controller = new AccountController();
        $response = $controller->index($this->deps);
        $this->assertNotEmpty($response);
    }

    public function testChangeEmailFormRuns() {
        $controller = new AccountController();
        $response = $controller->showChangeEmailForm($this->deps);
        $this->assertNotEmpty($response);
    }

    public function testChangePasswordFormRuns() {
        $controller = new AccountController();
        $response = $controller->showChangePasswordForm($this->deps);
        $this->assertNotEmpty($response);
    }

    public function testDeleteConfirmationFormRuns() {
        $controller = new AccountController();
        $response = $controller->deleteAccountConfirmation($this->deps);
        $this->assertNotEmpty($response);
    }

    public function testChangeEmailWithEmptyPassword() {
        RequestWrapper::inject(
            new Request([
                'newemail'     => 'new@localhost',
                'confirmemail' => 'new@localhost',
                'passw'        => '',
            ])
        );

        $controller = new AccountController();
        $response = $controller->changeEmail($this->deps);

        $reflection = new \ReflectionProperty(get_class($response), 'data');
        $reflection->setAccessible(true);
        $response_data = $reflection->getValue($response);
        $this->assertNotEmpty($response_data['error']);
    }

    public function testChangePasswordWithEmptyPassword() {
        RequestWrapper::inject(
            new Request([
                'newpassw'     => 'newpassword',
                'confirmpassw' => 'newpassword',
                'passw'        => '',
            ])
        );

        $controller = new AccountController();
        $response = $controller->changePassword($this->deps);
        //  Extract the good data from the StreamedViewResponse
        $reflection = new \ReflectionProperty(get_class($response), 'data');
        $reflection->setAccessible(true);
        $response_data = $reflection->getValue($response);
        $this->assertNotEmpty($response_data['error']);
    }

    public function testDeleteWithEmptyPassword() {
        RequestWrapper::inject(
            new Request([
                'passw'        => '',
            ])
        );

        $session = SessionFactory::getSession();
        $failure_count = $session->get('delete_attempts');
        $controller = new AccountController();
        $response = $controller->deleteAccount($this->deps);

        $reflection = new \ReflectionProperty(get_class($response), 'data');
        $reflection->setAccessible(true);
        $response_data = $reflection->getValue($response);
        $this->assertNotEmpty($response_data['error']);
        $this->assertGreaterThan($failure_count, $session->get('delete_attempts'));
    }
}
