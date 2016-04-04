<?php
function smarty_modifier_markdown($p_string) {
    return preg_replace_callback(
        "/\[href:([^\[\]]+)\|([^\[\]]+)\]/",
        function ($matches) {
            return '<a href="'.$matches[1].'">'.$matches[2].'</a>';
        },
        $p_string
    );
}
