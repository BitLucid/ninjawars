<?php
/*
* resources.php build defaults. Used in CI builds, not live, not local dev
* It defines constants used throughout the application.  Constants for tracked files 
*/
define('DATABASE_USE_PASSWORD', false); // *** Whether to specify password to pdo at all. Generally true only on live
define('DATABASE_USE_PORT', false); // *** Whether to specify port to pdo at all. Generally true only on live
define('DATABASE_HOST', "localhost");		// *** The host to connect to for the database, localhost by default
define('DATABASE_PORT', "5432");		// *** The port number to connect on.
define('DATABASE_USER', "postgres");		// *** The user that should connect to the database
define('DATABASE_PASSWORD', "unused_in_build");		// *** The password for the database connection, trust on dev
define('DATABASE_NAME', "nw");		// *** The name of the database to connect to, nw on dev
define('OFFLINE', false);				// *** Controls if remote or local resources are used
define('DEBUG', false);					// *** Shorter debugging constant name, set as false on live.
define('PROFILE', false);				// *** Whether or not to do performance profiling
define('DEBUG_ALL_ERRORS', false);	// *** Second debugging level, e.g. email debugging, only works when debug is also on.
define('SERVER_ROOT', realpath(__DIR__).'/');		// *** The root deployment directory of the game
// Generally for the install purposes the SERVER_ROOT should correspond to /srv/ninjawars/deploy/ 
define('WEB_ROOT', "http://nw.remote/");			// *** The base URL used to access the game, http://www.ninjawars.net on live
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

define('TRAP_ERRORS', true); // Whether to use the global error handler & oops page, true on live.

define('TEMPLATE_LIBRARY_PATH', SERVER_ROOT.'vendor/smarty/smarty/distribution/libs/Smarty.class.php'); // Path to Smarty 3

// For location-specific, can-be-dynamic-or-not constants.
define('COMPILED_TEMPLATE_PATH', SERVER_ROOT.'templates/compiled/'); // *** This folder must have write permissions.
define('TEMPLATE_CACHING_PATH', SERVER_ROOT.'templates/cache/'); // *** This folder must have write permissions.
define('LOGS', SERVER_ROOT.'resources/logs/'); // *** For all custom logging
define('CONNECTION_STRING', 'pgsql:host='.DATABASE_HOST.';dbname='.DATABASE_NAME.';user='.DATABASE_USER.
	(DATABASE_USE_PORT?';port='.DATABASE_PORT:'').(DATABASE_USE_PASSWORD?';password='.DATABASE_PASSWORD:''));

// Seperate, tracked file for derived constants, that changes as they change.
require(SERVER_ROOT."derived_constants.php");

