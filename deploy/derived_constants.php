<?php
// These are the derived resource constants, which can be tracked.

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

// Defines for avatar options.
define('GRAVATAR', 1);
?>
