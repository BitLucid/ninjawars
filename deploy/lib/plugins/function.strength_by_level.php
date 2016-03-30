<?php
use NinjaWars\core\data\Player;
/**
 * Smarty plugin to wrap player strength calculation
 *
 * @param int $level Level of pc
 * @return int
 */
function smarty_function_strength_by_level($level) {
    return Player::baseStrengthByLevel((int)$level);
}
