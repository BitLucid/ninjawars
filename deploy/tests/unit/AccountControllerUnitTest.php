<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use NinjaWars\core\environment\RequestWrapper;
use NinjaWars\core\extensions\SessionFactory;
use NinjaWars\core\control\AccountController;

class AccountControllerUnitTest extends NWTest
{
    private $controller;

    public function __construct()
    {
        $this->controller = new AccountController();
    }

    public function setUp(): void
    {
        parent::setUp();
        $this->markTestIncomplete('AccountController::render relies on the DB and session.');
        SessionFactory::init(new MockArraySessionStorage());

        $get = [
            'command' => 'change'
        ];

        $request = new Request($get);

        RequestWrapper::inject($request); // Pass a request to be used by tests
    }

    public function tearDown(): void
    {
        RequestWrapper::destroy();
        $session = SessionFactory::getSession();
        $session->invalidate();
        parent::tearDown();
    }

    public function testIndex()
    {
        $response = $this->controller->index();

        $this->assertArrayHasKey('template', $response);
    }

    public function testShowChangeEmailForm()
    {
        $response = $this->controller->showChangeEmailForm();

        $this->assertArrayHasKey('template', $response);
    }

    public function testShowChangePasswordForm()
    {
        $response = $this->controller->showChangePasswordForm();

        $this->assertArrayHasKey('template', $response);
    }

    public function testDeleteAccountConfirmation()
    {
        $response = $this->controller->deleteAccountConfirmation();

        $this->assertArrayHasKey('template', $response);
    }
}
