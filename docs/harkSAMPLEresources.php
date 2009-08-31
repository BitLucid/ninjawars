<?php
/*
* resources.php template. This file must be included whenever a script is run. It defines constants used throughout the application
*/
define('DATABASE_HOST', "localhost");		// *** The host to connect to for the database
define('DATABASE_USER', "tchalvak");		// *** The user that should connect to the database
define('DATABASE_NAME', "ninjawars");		// *** The name of the database to connect to
define('OFFLINE', false);				// *** Controls if remote or local resources are used
define('DEBUG', true);					// *** Shorter debugging constant name, set as false on live.
define('DEBUG_ALL_ERRORS', true);	// *** Only will turn on if debug is also on.
define('SERVER_ROOT', "/home/tchalvak/ninjawars/deploy/");		// *** The root deployment directory of the game
define('WEB_ROOT', "http://127.0.0.1/ninjawars/deploy/www/");			// *** The base URL used to access the game
define('ADMIN_EMAIL', "ninjawarsTchalvak@gmail.com");		// *** For logs/emailed errors.
define('SUPPORT_EMAIL', "ninjawarsTchalvak@gmail.com");	// *** For public questions.
define('SUPPORT_EMAIL_FORMAL_NAME', "Ninjawars Tchalvak");
define('SYSTEM_MESSENGER_EMAIL', "noreply@ninjawars.net");
define('SYSTEM_MESSENGER_NAME', "Automated Ninjawars Messenger");


// For location-specific derived-or-not constants.
define('COMPILED_TEMPLATE_PATH', SERVER_ROOT.'templates/compiled/'); // *** This folder must have write permissions.
define('LOGS', SERVER_ROOT.'cron/'); // *** For all custom logging
define('CONNECTION_STRING', 'pgsql:dbname='.DATABASE_NAME.';user='.DATABASE_USER);

// Seperate, tracked file for derived constants, that changes as they change.
require(SERVER_ROOT."derived_constants.php");
?>
