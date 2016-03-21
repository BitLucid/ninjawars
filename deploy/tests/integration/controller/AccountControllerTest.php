<?php
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use NinjaWars\core\control\AccountController;
use NinjaWars\core\extensions\SessionFactory;
use NinjaWars\core\environment\RequestWrapper;

class AccountControllerTest extends PHPUnit_Framework_TestCase {
	public function setUp() {
		SessionFactory::init(new MockArraySessionStorage());
        $char_id = TestAccountCreateAndDestroy::create_testing_account();
		SessionFactory::getSession()->set('player_id', $char_id);
	}

	public function tearDown() {
        $session = SessionFactory::getSession();
        $session->invalidate();
    }

    public function testInstantiation() {
        $controller = new AccountController();
        $this->assertInstanceOf('NinjaWars\core\control\AccountController', $controller);
    }

    public function testIndexRuns() {
        $controller = new AccountController();
        $response = $controller->index();
        $this->assertNotEmpty($response);
    }

    public function testChangeEmailFormRuns() {
        $controller = new AccountController();
        $response = $controller->showChangeEmailForm();
        $this->assertNotEmpty($response);
    }

    public function testChangePasswordFormRuns() {
        $controller = new AccountController();
        $response = $controller->showChangePasswordForm();
        $this->assertNotEmpty($response);
    }

    public function testDeleteConfirmationFormRuns() {
        $controller = new AccountController();
        $response = $controller->deleteAccountConfirmation();
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
        $response = $controller->changeEmail();
        $this->assertNotEmpty($response['parts']['error']);
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
        $response = $controller->changePassword();
        $this->assertNotEmpty($response['parts']['error']);
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
        $response = $controller->deleteAccount();
        $this->assertNotEmpty($response['parts']['error']);
        $this->assertGreaterThan($failure_count, $session->get('delete_attempts'));
    }
}
