<?php
function smarty_modifier_css_classify($p_string) {
    return strtolower(str_replace(' ', '-', $p_string));
}
