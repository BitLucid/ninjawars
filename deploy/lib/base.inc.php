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
require_once(LIB_ROOT.'environment/status_defines.php'); // Status constant definitions.
require_once(SERVER_ROOT.'npc-list.php'); // must not be before status defines
require_once(LIB_ROOT.'environment/global_error_handling.php');

require_once(TEMPLATE_LIBRARY_PATH);
require_once(LIB_ROOT.'control/lib_helpers.php');
require_once(LIB_ROOT.'control/lib_crypto.php');
require_once(LIB_ROOT.'data/database.php'); // Eloquent database connection
require_once(LIB_ROOT.'data/lib_db.php');
require_once(LIB_ROOT.'extensions/Nmail.class.php');
