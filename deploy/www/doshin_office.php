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
			$doshin->offerBounty();
			break;
		case 'Bribe':
			$doshin->bribe();
			break;
		default:
			$doshin->index();
			break;
	}
}
