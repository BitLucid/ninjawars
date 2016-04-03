<?php
use NinjaWars\core\data\Player;
use NinjaWars\core\data\Account;

/**
 * Class to house static methods for killing characters of players with multis
 */
class CloneKill {
    public static function canKill($clone1, $clone2) {
        // Input is transformed into
        if(!$clone1 instanceof Player){
            if($clone1 == positive_int($clone1)){
                $char1 = Player::find($clone1);
            } elseif(is_string($clone1)){
                $char1 = Player::find($clone1);
            }
        } else {
            $char1 = $clone1;
        }
        if(!$clone2 instanceof Player){
            if($clone2 == positive_int($clone2)){
                $char2 = Player::find($clone2);
            } elseif(is_string($clone2)){
                $char2 = Player::find($clone2);
            }
        } else {
            $char2 = $clone2;
        }

        // Reject invalid/nonexistent characters
        if($char1 === null || $char2 === null){
            return false;
        }

        // Reject same character
        if($char1->id() == $char2->id()){
            return false;
        }

        // Don't clone kill admins.
        if($char1->isAdmin() || $char2->isAdmin()){
            return false;
        }
        // Reject inactive characters
        if(!$char1->isActive() || !$char2->isActive()){
            return false;
        }

        // TODO: Reject inoperative characters
        // TODO: You can't clone kill yourself..

        $host= gethostname();
        $server_ip = gethostbyname($host);

        $untouchable_ips = ['127.0.0.1', '173.203.99.229', $server_ip, '', null];

        $account1 = Account::findByChar($char1);
        $account2 = Account::findByChar($char2);

        // Reject invalid custom ips
        if(in_array($account1->getLastIp(), $untouchable_ips) || in_array($account2->getLastIp(), $untouchable_ips)){
            return false;
        }

        // If characters have the same joint account, and have been logged in recently...
        if($account1->getLastIp() === $account2->getLastIp()){ // Activity was already tested above.
            return true;
        }

        return false;
    }

    /**
     * Perform the effects of a clonekill.
     * @return string outcome or false
     */
    public static function kill(Player $self, Player $clone1, Player $clone2) {
            if(self::canKill($clone1, $clone2)){
                $today = date("F j, Y, g:i a");
                $clone1_health = $clone1->health();
                $clone2_health = $clone2->health();
                $clone1_turns = $clone1->turns;
                $clone2_turns = $clone2->turns;
                $clone1->changeTurns(-1*$clone1->turns);
                $clone1->death();
                $clone2->changeTurns(-1*$clone2->turns);
                $clone2->death();
                $result_message = "You obliterate the clone {$clone1->name()} for $clone1_health health, $clone1_turns turns
                     and the clone {$clone2->name()} for $clone2_health health, $clone2_turns turns.";
                send_event($self->id(), $clone1->id(), "You and {$clone2->name()} were Clone Killed at $today.");
                send_event($self->id(), $clone2->id(), "You and {$clone1->name()} were Clone Killed at $today.");
                return $result_message;
            } else {
                return false;
            }
    }
}
