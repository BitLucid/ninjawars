<?php
// Unicode for IChing options... http://www.fileformat.info/info/unicode/block/yijing_hexagram_symbols/images.htm
require_once(CORE.'control/CasinoController.php');

use app\Controller\CasinoController as CasinoController;

if ($error = init(CasinoController::PRIV, CasinoController::ALIVE)) {
	display_error($error);
} else {
	$casino = new CasinoController();

	$command = in('command');

	switch ($command) {
		case 'bet':
			$response = $casino->bet();
			break;
		default:
			$response = $casino->index();
			break;
	}

	display_page(
		$response['template'],
		$response['title'],
		$response['parts'],
		$response['options']
	);
}
