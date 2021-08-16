<?php
require_once(substr(__FILE__, 0, (strpos(__FILE__, 'cron/'))).'resources.php');
require_once(ROOT.'cron/cron_bootstrap.php');

use NinjaWars\core\control\TickController;
use NinjaWars\core\data\GameLog;
use NinjaWars\core\data\Deity;

// Hack to keep players from resurrecting beyond the bounds of what could be expected
$res = query('update players set health = greatest(150, (50+stamina*5)) where health < 1 and active !=0');
echo "Players updated: ";
echo ($res->rowCount());
