<?php
// These are the derived resource constants, which can be tracked.

define('VENDOR_ROOT', SERVER_ROOT.'vendor/'); // Composer vendors

define('ROOT', SERVER_ROOT);		// *** Another alias for the root.
define('CONF_ROOT', SERVER_ROOT.'conf/'); // Configuration root.
define('CORE', SERVER_ROOT.'lib/');

define('CSS_ROOT', WEB_ROOT.'css/');
define('JS_ROOT', WEB_ROOT.'js/');

define('IMAGE_ROOT', WEB_ROOT.'images/');
define('SERVER_IMAGE_ROOT', SERVER_ROOT.'images/');

// *** Add in specific object folders as they get developed.
define('LIB_ROOT', SERVER_ROOT.'lib/');
define('DB_ROOT', LIB_ROOT.'data/');

define('TEMPLATE_PATH', SERVER_ROOT.'templates/'); // ** For templates.
define('TEMPLATE_PLUGIN_PATH', SERVER_ROOT.'lib/plugins/'); // ** For template plugins.
// COMPILED_TEMPLATE_PATH is kept in resources since it requires write permissions.

define('LOCAL_JS', (DEBUG||OFFLINE));
define('MAX_MSG_LENGTH', 750);
define('MAX_CLAN_MSG_LENGTH', MAX_MSG_LENGTH - strlen('clan: '));

// username upper and lower
define('UNAME_LOWER_LENGTH', 3);
define('UNAME_UPPER_LENGTH', 24);

define('MAX_PLAYER_LEVEL', 350);

// Defines for avatar options.
define('GRAVATAR', 1);

define('NEW_PLAYER_INITIAL_STATS', 5);
define('NEW_PLAYER_INITIAL_HEALTH', 150);
define('LEVEL_UP_STAT_RAISE', 5);
define('LEVEL_UP_HP_RAISE', 25);

// Constants for deity scripts
define('MIN_PLAYERS_FOR_UNCONFIRM', 1000);
define('MIN_DAYS_FOR_UNCONFIRM',    60);
define('MAX_PLAYERS_TO_UNCONFIRM',  200);
define('POISON_DAMAGE',             50);
define('ONLINE_TIMEOUT',            '70 hours'); // Max time a person is kept online without being active.
define('TURN_REGEN_PER_TICK',       2);
define('TURN_REGEN_THRESHOLD',      100);
define('HEALTH_REGEN_THRESHOLD',    200);
define('HEALTH_REGEN_PER_TICK',     8);
define('KI_REGEN_PER_TICK',         1);
define('KI_REGEN_TIMEOUT',          '6 minutes');

define('MINOR_REVIVE_THRESHOLD',    70);
define('MAJOR_REVIVE_PERCENT',      7);

define('DEITY_LOG_CHANCE_DIVISOR',  60);

define('RANK_WEIGHT_LEVEL',      5000);
define('RANK_WEIGHT_GOLD',       200);
define('RANK_WEIGHT_INACTIVITY', 200);
