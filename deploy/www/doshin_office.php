<?php
require_once(LIB_ROOT.'control/lib_inventory.php');
require_once(CORE.'control/DoshinController.php');

use app\Controller\DoshinController;


if ($error = init(DoshinController::$private, DoshinController::$alive)) {
	display_error($error);
} else {
	$doshin = new DoshinController();

	$command = in('command');

	switch ($command) {
		case 'Offer Bounty':
			$response = $doshin->offerBounty();
			break;
		case 'Bribe':
			$response = $doshin->bribe();
			break;
		default:
			$response = $doshin->index();
			break;
	}

	display_page(
		$response['template'],
		$response['title'],
		$response['parts'],
		$response['options']
	);
}
