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
        $levelDifference = ($target->level - $attacker->level);

        if ($levelDifference > 10) {
            $multiplier = 5;
        } else if ($levelDifference > 0) {
            $multiplier = ceil($levelDifference/2);  //killpoint return of half the level difference.
        } else {
            $multiplier = 0;
        }

        return 1+$multiplier;
    }

    public static function runBountyExchange($username, $defender) {  //  *** BOUNTY EQUATION ***
        $user = Player::findByName($username);
        $defender = Player::findByName($defender);

        if ($defender->bounty > 0) {
            $user->set_gold($user->gold + $defender->bounty);
            $user->save();

            $defender->set_bounty(0);
            $defender->save();

            // *** Reward bounty whenever available. ***
            return "You have received the {$defender->bounty} gold bounty on $defender's head for your deeds!";
            $bounty_msg = "You have valiantly slain the wanted criminal, $defender! For your efforts, you have been awarded {$defender->bounty} gold!";
            sendMessage("Village Doshin", $username, $bounty_msg);
        } else {
            // *** Bounty Increase equation: (attacker's level - defender's level) / an increment, rounded down ***
            $levelRatio     = floor(($user->level - $defender->level) / 10);
            $bountyIncrease = min(25, max($levelRatio * 25, 0));	//Avoids negative increases, max of 30 gold, min of 0

            if ($bountyIncrease > 0) {
                // *** If Defender has no bounty and there was a level difference. ***
                $user->set_bounty($user->bounty + $bountyIncrease);
                $user->save();

                return "Your victim was much weaker than you. The townsfolk are angered. A bounty of $bountyIncrease gold has been placed on your head!";
            } else {
                return null;
            }
        }
    }
}
