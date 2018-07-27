<?php
require_once(substr(__FILE__, 0, (strpos(__FILE__, 'cron/'))).'resources.php');
require_once(ROOT.'cron/cron_bootstrap.php');

use NinjaWars\core\control\TickController;
use NinjaWars\core\data\GameLog;
use NinjaWars\core\data\Deity;

query('update players set health = case when (stamina*5)<100 then 150 else (50+stamina*5) end where active !=0');