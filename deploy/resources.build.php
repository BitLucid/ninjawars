<?php
/*
* resources.php template. The live version of this file must be included whenever a script is run. 
* It defines constants used throughout the application.  Constants for tracked files 
*/
define('DATABASE_HOST', "localhost");		// *** The host to connect to for the database, localhost by default
define('DATABASE_USER', "__REPLACE_ME_PG_USER__");		// *** The user that should connect to the database
define('DATABASE_NAME', "nw");		// *** The name of the database to connect to, nw on dev
define('OFFLINE', false);				// *** Controls if remote or local resources are used
define('DEBUG', true);					// *** Shorter debugging constant name, set as false on live.
define('PROFILE', true);				// *** Whether or not to do performance profiling
define('DEBUG_ALL_ERRORS', true);	// *** Second debugging level, e.g. email debugging, only works when debug is also on.
define('SERVER_ROOT', realpath(__DIR__).'/');		// *** The root deployment directory of the game
// Generally for the install purposes the SERVER_ROOT should correspond to /srv/ninjawars/deploy/ 
define('WEB_ROOT', "http://nw.local/");			// *** The base URL used to access the game, http://www.ninjawars.net on live
define('ADMIN_EMAIL', "ninjawarsTchalvak@gmail.com");		// *** For logs/emailed errors.
define('SUPPORT_EMAIL', "ninjawarsTchalvak@gmail.com");	// *** For public questions.
define('SUPPORT_EMAIL_NAME', "Ninjawars Tchalvak");
define('SYSTEM_EMAIL', "noreply@ninjawars.net");
define('SYSTEM_MESSENGER_EMAIL', "noreply@ninjawars.net");
define('SYSTEM_EMAIL_NAME', "Automated Ninjawars Messenger");
define('SYSTEM_MESSENGER_NAME', SYSTEM_EMAIL_NAME);
define('ALERTS_EMAIL', SUPPORT_EMAIL);

define('FACEBOOK_APP_ID', '30479872633'); // Non-confidential id for the facebook app
define('FACEBOOK_APP_SECRET', 'mooMooIAmACow'); // Secret! string for facebook login auth stuff.

define('TRAP_ERRORS', false); // Whether to use the global error handler & oops page.

define('TEMPLATE_LIBRARY_PATH', SERVER_ROOT.'vendor/smarty/smarty/distribution/libs/Smarty.class.php'); // Path to Smarty 3

// For location-specific, can-be-dynamic-or-not constants.
define('COMPILED_TEMPLATE_PATH', SERVER_ROOT.'templates/compiled/'); // *** This folder must have write permissions.
define('TEMPLATE_CACHING_PATH', SERVER_ROOT.'templates/cache/'); // *** This folder must have write permissions.
define('LOGS', SERVER_ROOT.'resources/logs/'); // *** For all custom logging
define('CONNECTION_STRING', 'pgsql:dbname='.DATABASE_NAME.';user='.DATABASE_USER);

// Seperate, tracked file for derived constants, that changes as they change.
require(SERVER_ROOT."derived_constants.php");

