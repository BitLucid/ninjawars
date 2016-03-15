<?php
require_once(substr(__FILE__, 0, (strpos(__FILE__, 'cron/'))).'resources.php');
require_once(dirname(__DIR__.'..').'/core/base.inc.php');
require_once(LIB_ROOT.'data/DatabaseConnection.php');
require_once(LIB_ROOT.'environment/lib_assert.php');
require_once(LIB_ROOT.'environment/status_defines.php'); // Status constant definitions.
require_once(LIB_ROOT.'environment/lib_error_reporting.php');
require_once(LIB_ROOT.'data/lib_db.php');
require_once(LIB_ROOT."control/lib_deity.php"); // Deity-specific functions
require_once(CORE.'data/Deity.php');

use NinjaWars\core\data\DatabaseConnection;
use NinjaWars\core\data\Deity;

Deity::tick(30);
