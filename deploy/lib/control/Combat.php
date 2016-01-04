<?php
/**
 * Combat behaviors during standard attacking.
 */
namespace NinjaWars\core\control;

use \Player;

class Combat {
    /**
     * Take an attacker and target, and return the killpoints
     * return int
     */
    public static function killpointsFromDueling(Player $attacker, Player $target) {
        $levelDifference = ($target->level() - $attacker->level());

        if ($levelDifference > 10) {
            $multiplier = 5;
        } else if ($levelDifference > 0) {
            $multiplier = ceil($levelDifference/2);  //killpoint return of half the level difference.
        } else {
            $multiplier = 0;
        }

        return 1+$multiplier;
    }
}
