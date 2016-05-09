<?php
require_once(CORE.'data/Deity.php');
require_once(CORE.'control/TickController.php');

use NinjaWars\core\control\TickController;

TickController::run('Deity', 1);