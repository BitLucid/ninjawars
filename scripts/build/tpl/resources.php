<?php

/*
* resources.php template. The live version of this file must be included whenever a script is run. 
* It defines constants used throughout the application.  Constants for tracked files 
*/
define('DATABASE_HOST', "localhost");		// *** The host to connect to for the database
define('DATABASE_USER', "kzqai");		// *** The user that should connect to the database
define('DATABASE_NAME', "nw");		// *** The name of the database to connect to
define('OFFLINE', false);				// *** Controls if remote or local resources are used
define('DEBUG', true);					// *** Shorter debugging constant name, set as false on live.
define('PROFILE', false);				// *** Whether or not to do performance profiling
define('DEBUG_ALL_ERRORS', true);	// *** Second debugging level, e.g. email debugging, only works when debug is also on.
define('SERVER_ROOT', realpath(__DIR__).DIRECTORY_SEPARATOR);		// *** The root deployment directory of the game
// Generally for the install purposes the SERVER_ROOT should correspond to /srv/ninjawars/deploy 
define('WEB_ROOT', "http://nw.local/");			// *** The base URL used to access the game
define('ADMIN_EMAIL', "ninjawarsTchalvak@gmail.com");		// *** For logs/emailed errors.
define('SUPPORT_EMAIL', "ninjawarsTchalvak@gmail.com");	// *** For public questions.
define('SUPPORT_EMAIL_NAME', "Ninjawars Tchalvak");
define('SUPPORT_EMAIL_FORMAL_NAME', SUPPORT_EMAIL_NAME); // redundancies for now
define('SYSTEM_EMAIL', "noreply@ninjawars.net");
define('SYSTEM_MESSENGER_EMAIL', "noreply@ninjawars.net");
define('SYSTEM_EMAIL_NAME', "Automated Ninjawars Messenger");
define('SYSTEM_MESSENGER_NAME', SYSTEM_EMAIL_NAME);
define('ALERTS_EMAIL', SUPPORT_EMAIL);
define('TRAP_ERRORS', false); // Whether to use the global error handler & oops page.

define('TEMPLATE_LIBRARY_PATH', 'smarty/Smarty.class.php'); // Template path for system install, /usr/share/php/smarty/Smarty.class.php, for example.

// For location-specific, can-be-dynamic-or-not constants.
define('COMPILED_TEMPLATE_PATH', SERVER_ROOT.'templates/compiled/'); // *** This folder must have write permissions.
define('LOGS', SERVER_ROOT.'cron/'); // *** For all custom logging
define('CONNECTION_STRING', 'pgsql:dbname='.DATABASE_NAME.';user='.DATABASE_USER);

// Seperate, tracked file for derived constants, that changes as they change.
require(SERVER_ROOT."derived_constants.php");