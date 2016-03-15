<?php

use \NinjaWars\core\data\Skill;
/**
 * Smarty plugin to determine turn cost of a skill
 *
 * @param Array $p_params Dictionary provided by Smarty
 * @return String
 */
function smarty_function_getTurnCost($p_params) {
    $skillListObj = new Skill();
    return $skillListObj->getTurnCost($p_params['skillName']);
}
