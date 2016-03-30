<?php
use NinjaWars\core\data\Player;
/**
 * Smarty plugin to wrap player stamina calculation
 *
 * @param int $level Level of pc
 * @return int
 */
function smarty_function_stamina_by_level($level) {
    return Player::baseStaminaByLevel((int)$level);
}
