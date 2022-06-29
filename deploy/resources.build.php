<?php
/*
* resources.php build defaults. Used in CI builds, not live, not local dev
* It defines constants used throughout the application.  Constants for tracked files
*/
define('DATABASE_USE_PASSWORD', false); // *** Whether to specify password to pdo at all. Generally true only on live
define('DATABASE_USE_PORT', false); // *** Whether to specify port to pdo at all. Generally true only on live
define('DATABASE_USE_HOST', false); // *** Whether to specify HOST to pdo at all. Generally true only on live
define('DATABASE_HOST', "localhost");		// *** The host to connect to for the database, localhost by default
define('DATABASE_PORT', "5432");		// *** The port number to connect on.
define('DATABASE_USER', "postgres");		// *** The user that should connect to the database
define('DATABASE_PASSWORD', "unused_in_build");		// *** The password for the database connection, trust on dev
define('DATABASE_NAME', "nw");		// *** The name of the database to connect to, nw on dev
define('OFFLINE', false);				// *** Controls if remote or local resources are used
define('DEBUG', true);					// *** Shorter debugging constant name, set as false on live.
define('SERVER_ROOT', realpath(__DIR__).'/');		// *** The root deployment directory of the game
// Generally for the install purposes the SERVER_ROOT should correspond to /srv/ninjawars/deploy/
define('WEB_ROOT', "http://localhost:8765/");			// *** The base URL used to access the game, http://www.ninjawars.net on live
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

define('TEMPLATE_LIBRARY_PATH', SERVER_ROOT.'vendor/smarty/smarty/libs/Smarty.class.php'); // Path to Smarty 3

// For location-specific, can-be-dynamic-or-not constants.
define('COMPILED_TEMPLATE_PATH', SERVER_ROOT.'templates/compiled/'); // *** This folder must have write permissions.
define('TEMPLATE_CACHING_PATH', SERVER_ROOT.'templates/cache/'); // *** This folder must have write permissions.
define('LOGS', SERVER_ROOT.'resources/logs/'); // *** For all custom logging
define('CONNECTION_STRING', 'pgsql:'.(DATABASE_USE_HOST ? 'host='.DATABASE_HOST : '').';dbname='.DATABASE_NAME.';user='.DATABASE_USER.
    (DATABASE_USE_PORT ? ';port='.DATABASE_PORT : '').(DATABASE_USE_PASSWORD ? ';password='.DATABASE_PASSWORD : ''));

// Can't use constants as arrays until php 7, so static class info instead.
class Constants
{
    public static $trusted_proxies = ['104.130.111.36', '10.189.245.10'];
}

if (true) {
    define('NEW_PLAYER_INITIAL_STATS', 5);
    define('NEW_PLAYER_INITIAL_HEALTH', 90); // Actually, base health, since stats add to this even at level 1.
    define('LEVEL_UP_STAT_RAISE', 5);
    define('LEVEL_UP_HP_RAISE', 25);
} else { // Communism
    define('NEW_PLAYER_INITIAL_STATS', 10);
    define('NEW_PLAYER_INITIAL_HEALTH', 5); // Actually, base health.
    define('LEVEL_UP_STAT_RAISE', 0);
    define('LEVEL_UP_HP_RAISE', 0);
}

// Seperate, tracked file for derived constants, that changes as they change.
require(SERVER_ROOT."derived_constants.php");
