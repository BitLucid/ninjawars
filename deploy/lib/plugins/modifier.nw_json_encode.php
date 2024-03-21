<?php

/**
 * Just runs json_encode in smarty template
 */
function smarty_modifier_nw_json_encode($p_string)
{
    return $p_string ? json_encode($p_string) : null;
}
