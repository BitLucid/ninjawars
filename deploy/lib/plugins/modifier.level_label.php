<?php
function smarty_modifier_level_label($p_level) {
    $level_labels = [
        [0, 'Novice'],
        [2, 'Acolyte'],
        [6, 'Ninja'],
        [31, 'Elder Ninja'],
        [101, 'Shadow Master'],
    ];

    $label = current($level_labels);

    foreach ($level_labels AS $level_label) {
        if ($p_level > $level_label[0]) {
            $label = $level_label[1];
        } else {
            break;
        }
    }

    return $label;
}
