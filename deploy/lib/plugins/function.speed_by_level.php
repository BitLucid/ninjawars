<?php
use NinjaWars\core\data\Player;
/**
 * Smarty plugin to wrap player speed calculation
 *
 * @param int $level Level of pc
 * @return int
 */
function smarty_function_speed_by_level($level) {
    return Player::baseSpeedByLevel((int)$level);
}
