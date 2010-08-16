<?php
function globalExceptionHandler($e) {
	$msg = "Exception message: ".$e."\r\n\r\n";

	error_log($e);

	sendErrorEmail($msg);
	showErrorPage();

	exit(1);
}

function globalErrorHandler($errno, $errstr, $errfile, $errline) {
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
        "$errors on line $errline in file $errfile";

	sendErrorEmail($msg);
	showErrorPage();

	exit(1);
}

function sendErrorEmail($p_errorMsg) {
	if (isset($_SESSION['account_id'])) {
		$p_errorMsg .= "Error Occured for accountID ".$_SESSION['account_id']."\r\n";
	}

	if (isset($_SESSION['player_id'])) {
		$p_errorMsg .= "Error Occured for playerID ".$_SESSION['player_id']."\r\n";
	}

	$headers = "MIME-Version: 1.0\r\n".
		"Content-Type: text/plain; charset=ISO-8859-15\r\n".
		"To: ".ALERTS_EMAIL."\r\n".
		"From: ".SYSTEM_MESSENGER_EMAIL."\r\n".
		'X-Mailer: PHP/' . phpversion();

	mail(ALERTS_EMAIL, 'Ninjawars: Error', $p_errorMsg, $headers);
}

function showErrorPage() {
	if (headers_sent()) {
		echo "<script type='text/javascript'>location.href = 'error.html';</script>";
	} else {
		header('Location: /error.html');
	}
}

set_exception_handler('globalExceptionHandler');
set_error_handler('globalErrorHandler', E_USER_ERROR);
?>
