<?php
require_once(CORE.'control/ShrineController.php');

use app\Controller\ShrineController;

if ($error = init(ShrineController::PRIV, ShrineController::ALIVE)) {
	display_error($error);
} else {
	$shrine = new ShrineController();

	switch (in('command')) {
		case 'heal':
			$response = $shrine->heal();
			break;
		case 'resurrect':
			$response = $shrine->resurrect();
			break;
		case 'heal_and_resurrect':
			$response = $shrine->healAndResurrect();
			break;
		case 'cure':
			$response = $shrine->cure();
			break;
		default:
			$response = $shrine->index();
			break;
	}

	display_page(
		$response['template'],
		$response['title'],
		$response,
		$response['options']
	);
}
