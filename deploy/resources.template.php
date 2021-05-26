<?php
/*
* resources.php template. The live version of this file must be included whenever a script is run. 
* It defines constants used throughout the application.  Constants for tracked files 
*/
define('DATABASE_USE_PASSWORD', true); // *** Whether to specify password to pdo at all. Generally true only on live
define('DATABASE_USE_PORT', true); // *** Whether to specify port to pdo at all. Generally true only on live
define('DATABASE_USE_HOST', false); // *** Whether to specify HOST to pdo at all. Generally true only on live
define('DATABASE_HOST', __DB_HOST__);		// *** The host to connect to for the database
define('DATABASE_PORT', __DB_PORT__);		// *** The port number to connect on.
define('DATABASE_USER', __DB_USER__);		// *** The user that should connect to the database
define('DATABASE_NAME', __DB_NAME__);		// *** The name of the database to connect to
define('DATABASE_PASSWORD', __DB_PASS__);		// *** The password for the database connection
define('OFFLINE', __OFFLINE__);				// *** Controls if remote or local resources are used
define('DEBUG', __DEBUG__);					// *** Shorter debugging constant name, set as false on live.
define('SERVER_ROOT', __SERVER_ROOT__);		// *** The root deployment directory of the game
// Generally for the install purposes the SERVER_ROOT should correspond to /srv/ninjawars/deploy/ 
define('WEB_ROOT', __WWW_ROOT__);			// *** The base URL used to access the game
define('ADMIN_EMAIL', __ADMIN_EMAIL__);		// *** For logs/emailed errors.
define('SUPPORT_EMAIL', __SUPPORT_EMAIL__);	// *** For public questions.
define('SUPPORT_EMAIL_NAME', __SUPPORT_EMAIL_NAME__);
define('SYSTEM_EMAIL', __SYSTEM_EMAIL__);
define('SYSTEM_EMAIL_NAME', __SYSTEM_EMAIL_NAME__);
define('SYSTEM_MESSENGER_EMAIL', __SYSTEM_EMAIL__);
define('SYSTEM_MESSENGER_NAME', SYSTEM_EMAIL_NAME);
define('ALERTS_EMAIL', __ALERTS_EMAIL__);

define('FACEBOOK_APP_ID', __FACEBOOK_APP_ID__); // Non-confidential id for the facebook app
define('FACEBOOK_APP_SECRET', __FACEBOOK_APP_SECRET__); // Secret string for facebook login auth stuff.

define('TRAP_ERRORS', __TRAP_ERRORS__); // Whether to use the global error handler & oops page.

define('TEMPLATE_LIBRARY_PATH', SERVER_ROOT.'vendor/smarty/smarty/libs/Smarty.class.php'); // Path to Smarty 3

// For location-specific, can-be-dynamic-or-not constants.
define('COMPILED_TEMPLATE_PATH', SERVER_ROOT.'templates/compiled/'); // *** This folder must have write permissions.
define('TEMPLATE_CACHING_PATH', SERVER_ROOT.'templates/cache/'); // *** This folder must have write permissions.
define('LOGS', SERVER_ROOT.'resources/logs/'); // *** For all custom logging
define('CONNECTION_STRING', 'pgsql:'.(DATABASE_USE_HOST? 'host='.DATABASE_HOST : '').';dbname='.DATABASE_NAME.';user='.DATABASE_USER.
	(DATABASE_USE_PORT?';port='.DATABASE_PORT:'').(DATABASE_USE_PASSWORD?';password='.DATABASE_PASSWORD:''));

// Used, among other things, to check for attackLegal, can't use constants as arrays until php 7, so static class info instead.
class Constants {
    public static $trusted_proxies = ['104.130.111.36', '10.189.245.10', '52.204.80.200', '172.31.17.0', '172.31.54.175'];
}

define('NEW_PLAYER_INITIAL_STATS', 10);
define('NEW_PLAYER_INITIAL_HEALTH', 100);
define('LEVEL_UP_STAT_RAISE', 5);
define('LEVEL_UP_HP_RAISE', 5);

// Seperate, tracked file for derived constants, that changes as they change.
require(SERVER_ROOT."derived_constants.php");

