<?php

/**
 * Smarty plugin for ensuring some incoming untrusted data
 * can be used correctly in js when passing to js
 */
function smarty_modifier_to_integer($p_dirty)
{
    return (int) $p_dirty;
}
