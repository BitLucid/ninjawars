<?php
require_once(LIB_ROOT.'control/lib_clan.php');
require_once(CORE.'control/ClanController.php');

use app\Controller\ClanController;

if ($error = init(ClanController::PRIV, ClanController::ALIVE)) {
	display_error($error);
	die();
}

$command = (string)in('command');

$clanController = new ClanController();

switch ($command) {
	case 'view':
		$response = $clanController->view();
		break;
	case 'message':
		$response = $clanController->message();
		break;
	case 'update':
		$response = $clanController->update();
		break;
	case 'edit':
		$response = $clanController->edit();
		break;
	case 'new':
		$response = $clanController->create();
		break;
	case 'leave':
		$response = $clanController->leave();
		break;
	case 'kick':
		$response = $clanController->kick();
		break;
	case 'join':
		$response = $clanController->join();
		break;
	case 'invite':
		$response = $clanController->invite();
		break;
	case 'disband':
		$response = $clanController->disband();
		break;
	case 'list':
	default:
		$response = $clanController->listClans();
		break;
}

display_page(
	$response['template'],
	$response['title'],
	$response['parts'],
	$response['options']
);
