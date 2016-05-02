<?php
/**
 * Combat behaviors during standard attacking.
 */
namespace NinjaWars\core\control;

use NinjaWars\core\data\Player;
use NinjaWars\core\data\Character;

class Combat {
    const BOUNTY_MAX        = 5000;
    const BOUNTY_MULTIPLIER = 1;
    const BOUNTY_MIN        = 25;
    const KILLPOINTS_MIN    = 1;

    /**
     * Take an attacker and target, and return the killpoints
     *
     * @return int
     */
    public static function killpointsFromDueling(Player $attacker, Player $target) {
        $power_difference = ($target->difficulty() - $attacker->difficulty());

        $multiplier = max(0, min(4, ceil($power_difference/50)));

        return self::KILLPOINTS_MIN+$multiplier;
    }

    /**
     * Rewards bounty if defender has some, or increments attacker bounty if power disparity
     *
     * @return string
     */
    public static function runBountyExchange(Player $user, $defender, $bounty_mod=0) {
        assert($defender instanceof Character); // 'cause can't typehint interfaces

        if ($defender instanceof Player && $defender->bounty > 0) {
            $bounty = $defender->bounty;
            $defender->setBounty(0);
            $defender->save();
            $user->setGold($user->gold + $bounty);
            $user->save();

            return "You have received the $bounty gold bounty on $defender's head for your deeds!";
        } else { // Add bounty to attacker only if defender doesn't already have bounty on them.
            $disparity     = (int) floor(($user->difficulty() - $defender->difficulty()) / 10);
            $bountyIncrease = min(self::BOUNTY_MIN, max(0, ($disparity * static::BOUNTY_MULTIPLIER + $bounty_mod)));

            // Cap the increase.
            if ($bountyIncrease + $user->bounty > static::BOUNTY_MAX) {
                $bountyIncrease = static::BOUNTY_MAX - $user->bounty;
            }

            if ($bountyIncrease > 0) {
                // If Defender has no bounty and there was a level difference
                $user->setBounty($user->bounty + $bountyIncrease);
                $user->save();

                return "Your victim was much weaker than you. The townsfolk are angered. A bounty of $bountyIncrease gold has been placed on your head!";
            } else {
                return '';
            }
        }
    }
}
