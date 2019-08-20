<?php

namespace NinjaWars\core;

/**
 * Filter & Sanitation wrappers
 */
class Filter{
    /**
     * Return a casting with a result of a positive int, or else zero.
     *
     * @Note
     * this function will cast strings with leading integers to those integers.
     * E.g. 555'sql-injection becomes 555
     */
    public static function toNonNegativeInt($num) {
        return ((int)$num == $num && (int)$num > 0? (int)$num : 0);
    }

    /**
     * Casts to an integer anything that can be cast that way non-destructively, otherwise null.
     */
    public static function toInt($dirty) {
        return $dirty == (int) $dirty? (int) $dirty : null;
        // Cast anything that can be non-destructively cast.
    }

    /**
     * Strip low and high ascii characters, leave standard keyboard characters
     */
    public static function toSimple($dirty){
        return filter_var(
            str_replace(['"', '\''], '', $dirty),
            FILTER_SANITIZE_STRING, 
            FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH
        );
    }
}