<?php
/**
 * The starting include file for all of NW.
 *
 * @package lib
 * @subpackage base
 */

// Cut down on the global includes, use specific includes instead.

require_once(substr(__FILE__, 0, (strpos(__FILE__, 'lib/'))).'resources.php');

if (defined('PROFILE') && PROFILE) {
	$mtime = microtime();
	$mtime = explode(" ",$mtime);
	$mtime = $mtime[1] + $mtime[0];
	$__starttime = $mtime;
}

// Bootstrap to vendor
require_once(VENDOR_ROOT.'autoload.php');
require_once(SERVER_ROOT.'routes.php');

// Includes that actually actively modify settings.
require_once(LIB_ROOT.'environment/lib_assert.php');
require_once(LIB_ROOT.'environment/status_defines.php'); // Status constant definitions.
require_once(LIB_ROOT.'environment/lib_error_reporting.php');
require_once(LIB_ROOT.'environment/global_error_handling.php');

// Standalone utilities
require_once(LIB_ROOT.'control/assignment_functions.php');
require_once(LIB_ROOT.'data/database.php'); // Eloquent database connection
require_once(LIB_ROOT.'extensions/Nmail.class.php');

// *** Include all common function includes here.
require_once(LIB_ROOT.'control/lib_input.php');
require_once(LIB_ROOT.'control/lib_output.php');
require_once(LIB_ROOT.'data/lib_db.php');
require_once(TEMPLATE_LIBRARY_PATH); // Require smarty
require_once(LIB_ROOT.'extensions/lib_templates.php');

// Development includes
if (defined('DEBUG') && DEBUG) {
	require_once(LIB_ROOT.'control/lib_dev.php');
}

require_once(LIB_ROOT.'control/lib_header.php');
require_once(LIB_ROOT.'control/lib_events.php');
require_once(LIB_ROOT.'control/lib_crypto.php');
require_once(LIB_ROOT.'control/lib_auth.php');
require_once(LIB_ROOT.'control/lib_inventory.php');
require_once(LIB_ROOT.'control/Player.class.php');
