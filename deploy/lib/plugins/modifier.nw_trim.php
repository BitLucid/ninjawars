<?php

/**
 * Wrapper for trim as a smarty template modifier
 * As trim as a modifier is now deprecated in smarty
 */
function smarty_modifier_nw_trim($p_string)
{
    return $p_string ? trim($p_string) : null;
}
