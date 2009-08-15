<?php
/*
* resources.php template. This file must be included whenever a script is run. It defines constants used throughout the application
*/
define('DATABASE_HOST', __DB_HOST__);		// *** The host to connect to for the database
define('DATABASE_USER', __DB_USER__);		// *** The user that should connect to the database
define('DATABASE_NAME', __DB_NAME__);		// *** The name of the database to connect to
define('OFFLINE', __OFFLINE__);				// *** Controls if remote or local resources are used
define('DEBUG', __DEBUG__);					// *** Shorter debugging constant name, set as false on live.
define('DEBUG_ALL_ERRORS', __DEBUG_ALL__);	// *** Only will turn on if debug is also on.
define('SERVER_ROOT', __SERVER_ROOT__);		// *** The root deployment directory of the game
define('WEB_ROOT', __WWW_ROOT__);			// *** The base URL used to access the game
define('ADMIN_EMAIL', __ADMIN_EMAIL__);		// *** For logs/emailed errors.
define('SUPPORT_EMAIL', __SUPPORT_EMAIL__);	// *** For public questions.
define('SUPPORT_EMAIL_FORMAL_NAME', __SUPPORT_EMAIL_NAME__);
define('SYSTEM_MESSENGER_EMAIL', __SYSTEM_EMAIL__);
define('SYSTEM_MESSENGER_NAME', __SYSTEM_EMAIL_NAME__);


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
