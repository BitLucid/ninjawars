<?php
require_once(CORE.'control/AccountController.php');

use app\Controller\AccountController;

if ($error = init(AccountController::PRIV, AccountController::ALIVE)) {
	display_error($error);
	die();
}

$controller = new AccountController;
$command = in('command');

// Switch between the different controller methods.
switch(true){
	case($command=='show_change_email_form'):
		$response = $controller->showChangeEmailForm();
		break;
	case($command=='change_email'):
		$response = $controller->changeEmail();
		break;
	case($command=='show_change_password_form'):
		$response = $controller->showChangePasswordForm();
		break;
	case($command=='change_password'):
		$response = $controller->changePassword();
		break;
	case($command=='show_confirm_delete_form'):
		$response = $controller->deleteAccountConfirmation();
		break;
	case($command=='delete_account'):
		$response = $controller->deleteAccount();
		break;
	case($command = 'index'):
	default:
		$command = 'index';
		$response = $controller->index();
		break;
}

// Display the page with the template, title or header vars, template parts, and page options
display_page($response['template'], $response['title'], $response['parts'], $response['options']);
