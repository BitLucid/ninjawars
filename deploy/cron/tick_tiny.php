<?php
require_once(substr(__FILE__, 0, (strpos(__FILE__, 'cron/'))).'resources.php');
require_once(ROOT.'cron/cron_bootstrap.php');

use NinjaWars\core\control\TickController;
use NinjaWars\core\data\GameLog;

$tick = new TickController(new GameLog());
$tick->tiny();
