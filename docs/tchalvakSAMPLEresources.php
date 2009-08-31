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
define('WEB_ROOT', "http://localhost/tchalvak/ninjawars/deploy/www/");			// *** The base URL used to access the game
define('ADMIN_EMAIL', "ninjawarsTchalvak@gmail.com");		// *** For logs/emailed errors.
define('SUPPORT_EMAIL', "ninjawarsTchalvak@gmail.com");	// *** For public questions.
define('SUPPORT_EMAIL_FORMAL_NAME', "Ninjawars Tchalvak");
define('SYSTEM_MESSENGER_EMAIL', "noreply@ninjawars.net");
define('SYSTEM_MESSENGER_NAME', "Automated Ninjawars Messenger");


// *************************************************
// *** CONSTANTS BELOW ARE DERIVED. DO NOT ALTER ***
// *** \/ \/ \/ \/ \/ \/ \/ \/ \/ \/ \/ \/ \/ \/ ***
// *************************************************

define('CONNECTION_STRING', 'pgsql:dbname='.DATABASE_NAME.';user='.DATABASE_USER);
define('CSS_ROOT', WEB_ROOT.'css/');
define('JS_ROOT', WEB_ROOT.'js/');

define('IMAGE_ROOT', WEB_ROOT.'images/');
define('SERVER_IMAGE_ROOT', SERVER_ROOT.'images/');

// *** Add in specific object folders as they get developed.
define('LIB_ROOT', SERVER_ROOT.'lib/');
define('FUNC_ROOT', LIB_ROOT.'func/');
define('DB_ROOT', SERVER_ROOT.'db/');
define('OBJ_ROOT', SERVER_ROOT.'obj/'); // *** For generic business objects.

// *** Specific in-game concepts categorized into their folders.
define('ACCOUNT_ROOT', SERVER_ROOT.'account/'); // *** For all the account objects.
define('ADMIN_ROOT', SERVER_ROOT.'admin/'); // *** For all the admin objects.
define('CHAR_ROOT', SERVER_ROOT.'char/'); // *** For all the in-game character objects.
define('AREA_ROOT', SERVER_ROOT.'area/'); // *** For all the in-game area or room objects.
define('NPC_ROOT', SERVER_ROOT.'npc/'); // *** For all the in-game npc objects.
define('CLAN_ROOT', SERVER_ROOT.'clan/');  // *** For all the in-game clan objects.
define('COMBAT_ROOT', SERVER_ROOT.'combat/');  // *** For all the in-game combat objects.
define('SKILL_ROOT', SERVER_ROOT.'skill/');  // *** For all the in-game skill objects.
define('ITEM_ROOT', SERVER_ROOT.'item/');  // *** For all the in-game item/inventory objects.
?>
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
define('WEB_ROOT', "http://localhost/tchalvak/ninjawars/deploy/www/");			// *** The base URL used to access the game
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
