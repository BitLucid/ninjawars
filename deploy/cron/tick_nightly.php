<?php
require_once(substr(__FILE__, 0, (strpos(__FILE__, 'cron/'))).'resources.php');
require_once(CORE.'base.inc.php');
require_once(CORE.'data/Deity.php');
require_once(CORE.'control/TickController.php');

use NinjaWars\core\control\TickController;

TickController::run('Deity', 1440);
