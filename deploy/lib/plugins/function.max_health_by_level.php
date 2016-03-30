<?php
use NinjaWars\core\data\Player;
/**
 * Smarty plugin to wrap player health calculation
 *
 * @param int $level Level of pc
 * @return int
 */
function smarty_function_max_health_by_level($level) {
    return Player::maxHealthByLevel((int)$level);
}
