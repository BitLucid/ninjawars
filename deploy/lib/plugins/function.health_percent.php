<?php
use NinjaWars\core\data\Player;

function smarty_function_health_percent($p_params) {
    $health = $p_params['health'];
    $level  = $p_params['level'];

    return min(100, round(($health/Player::maxHealthByLevel($level))*100));
}
