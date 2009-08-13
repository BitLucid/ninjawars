<?php
/*
* resources.php template. Copy this from the sample to resources.php and change the necessary constants
*/
define('DATABASE_HOST', DB_HOST); // *** localhost on live
define('DATABASE_USER', DB_USER); // *** knownUsername on live
define('DATABASE_NAME', DB_NAME); // *** ninjawarsLive on live

define('CONNECTION_STRING', 'pgsql:dbname='.DATABASE_NAME.';user='.DATABASE_USER); // *** Mostly static

define('DEBUG', DEBUG); // *** Shorter debugging constant name, set as false on live.
define('DEBUG_ALL_ERRORS', DEBUG_ALL); // *** Only will turn on if debug is also on.

define('SERVER_ROOT', SERVER_ROOT); // *** Server root is the webgame folder, not the true root.
// *** known web dir on live.
define('WEB_ROOT', WWW_ROOT); // *** The web root is also the webgame folder, not the true root.
// *** known web dir on live.

define('ADMIN_EMAIL', ADMIN_EMAIL); // *** For logs/emailed errors.

define('SUPPORT_EMAIL', SUPPORT_EMAIL); // *** For public questions.
define('SUPPORT_EMAIL_FORMAL_NAME', SUPPORT_EMAIL_NAME);

define('SYSTEM_MESSENGER_EMAIL', SYSTEM_EMAIL);
define('SYSTEM_MESSENGER_NAME', SYSTEM_EMAIL_NAME);

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
