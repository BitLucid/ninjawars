<?php
/*
* resources.php template. The live version of this file must be included whenever a script is run. 
* It defines constants used throughout the application.  Constants for tracked files 
*/
define('DATABASE_HOST', __DB_HOST__);		// *** The host to connect to for the database
define('DATABASE_USER', __DB_USER__);		// *** The user that should connect to the database
define('DATABASE_NAME', __DB_NAME__);		// *** The name of the database to connect to
define('OFFLINE', __OFFLINE__);				// *** Controls if remote or local resources are used
define('DEBUG', __DEBUG__);					// *** Shorter debugging constant name, set as false on live.
define('PROFILE', __PROFILE__);				// *** Whether or not to do performance profiling
define('DEBUG_ALL_ERRORS', __DEBUG_ALL__);	// *** Second debugging level, e.g. email debugging, only works when debug is also on.
define('SERVER_ROOT', __SERVER_ROOT__);		// *** The root deployment directory of the game
define('WEB_ROOT', __WWW_ROOT__);			// *** The base URL used to access the game
define('ADMIN_EMAIL', __ADMIN_EMAIL__);		// *** For logs/emailed errors.
define('SUPPORT_EMAIL', __SUPPORT_EMAIL__);	// *** For public questions.
define('SUPPORT_EMAIL_NAME', __SUPPORT_EMAIL_NAME__);
define('SYSTEM_EMAIL', __SYSTEM_EMAIL__);
define('SYSTEM_EMAIL_NAME', __SYSTEM_EMAIL_NAME__);
define('SYSTEM_MESSENGER_EMAIL', __SYSTEM_MESSENGER_EMAIL__);
define('SYSTEM_EMAIL_NAME', __SYSTEM_EMAIL_NAME__);
define('ALERTS_EMAIL', __ALERTS_EMAIL__);
define('TRAP_ERRORS', __TRAP_ERRORS__); // Whether to use the global error handler & oops page.

define('TEMPLATE_LIBRARY_PATH', 'smarty/Smarty.class.php'); // Template path for system install, /usr/share/php/smarty/Smarty.class.php, for example.

// For location-specific, can-be-dynamic-or-not constants.
define('COMPILED_TEMPLATE_PATH', SERVER_ROOT.'templates/compiled/'); // *** This folder must have write permissions.
define('LOGS', SERVER_ROOT.'resources/logs/'); // *** For all custom logging
define('CONNECTION_STRING', 'pgsql:dbname='.DATABASE_NAME.';user='.DATABASE_USER);

// Seperate, tracked file for derived constants, that changes as they change.
require(SERVER_ROOT."derived_constants.php");
?>
