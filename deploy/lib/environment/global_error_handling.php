<?php
use NinjaWars\core\extensions\SessionFactory;

class NWError{
	/**
	 * Send out error information, about as much as possible
	 */
	public static function sendErrorEmail($p_errorMsg) {
		$session = SessionFactory::getSession();
		if ($session->has('account_id')) {
			$p_errorMsg .= "Error Occured for accountID ".$session->get('account_id')."\r\n";
		}
		if ($session->has('player_id')) {
			$p_errorMsg .= "Error Occured for playerID ".$session->get('player_id')."\r\n";
		}
		$p_errorMsg .= 'REQUEST_URI: '.(isset($_SERVER['REQUEST_URI'])? $_SERVER['REQUEST_URI'] : null)."\r\n";
		$p_errorMsg .= 'REFERER: '.(isset($_SERVER['HTTP_REFERER'])? $_SERVER['HTTP_REFERER'] : null)."\r\n";
		$p_errorMsg .= 'METHOD: '.(isset($_SERVER['REQUEST_METHOD'])? $_SERVER['REQUEST_METHOD'] : null)."\r\n";
		if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
			$p_errorMsg .= 'POST_DATA: '.print_r($_POST, true)."\r\n";
		}
		$headers = "MIME-Version: 1.0\r\n".
			"Content-Type: text/plain; charset=ISO-8859-15\r\n".
			"To: ".ALERTS_EMAIL."\r\n".
			"From: ".SYSTEM_EMAIL."\r\n".
			'X-Mailer: PHP/' . phpversion();
		mail(ALERTS_EMAIL, 'Ninjawars: Error'.substr($p_errorMsg, 0, 170), $p_errorMsg, $headers);
	}

	/**
	 * Display the error page with 500 error code, redirect if page errored part-way through.
	 */
	public static function showErrorPage() {
		if (headers_sent()) {
			echo "<script type='text/javascript'>location.href = 'error.html';</script>";
		} else {
			header("HTTP/1.1 500 Internal Server Error");
			include(SERVER_ROOT.'www/error.html');
		}
	}

	public static function exceptionHandler($e) {
		$msg = "Exception message: ".$e."\r\n\r\n";
		error_log($e);
		NWError::sendErrorEmail($msg);
		NWError::showErrorPage();
		exit(1);
	}

	public static function errorHandler($errno, $errstr, $errfile, $errline) {
		switch ($errno) {
			case E_NOTICE:
			case E_USER_NOTICE:
				$errors = "Notice";
				break;
			case E_WARNING:
			case E_USER_WARNING:
				$errors = "Warning";
				break;
			case E_ERROR:
			case E_USER_ERROR:
				$errors = "Fatal Error";
				break;
			default:
				$errors = "Unknown Error";
				break;
		}

		error_log(sprintf("PHP %s:  %s in %s on line %d", $errors, $errstr, $errfile, $errline));
		$msg = "ERROR: [$errno] $errstr\r\n".
	        "$errors on line $errline in file $errfile\r\n";

		NWError::sendErrorEmail($msg);
		NWError::showErrorPage();

		exit(1);
	}

}
