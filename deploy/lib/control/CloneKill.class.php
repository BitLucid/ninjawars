<?php


class CloneKill{
    public static function canKill($clone1, $clone2){
        // Input is transformed into 
        $id1 = $id2 = null;
        if(!$clone1 instanceof Player){
            if($clone1 == positive_int($clone1)){
                $char1 = new Player($clone1);
            } elseif(is_string($clone1)){
                $char1 = new Player($clone1);
            }
        } else {
            $char1 = $clone1;
        }
        if(!$clone2 instanceof Player){
            if($clone2 == positive_int($clone2)){
                $char2 = new Player($clone2);
            } elseif(is_string($clone2)){
                $char2 = new Player($clone2);
            }
        } else {
            $char2 = $clone2;
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

        $fair_ips = ['127.0.0.1', $_SERVER['REMOTE_ADDR'], '', null];

        // Reject invalid custom ips
        if(in_array($char1->ip(), $fair_ips) || in_array($char2->ip(), $fair_ips)){
            return false;
        }

        // If characters have the same joint account, and have been logged in recently...
        if($char1->ip() === $char2->ip()){ // Activity was already tested above.
            return true;
        }

        return false;
    }

    private static function kill(Player $clone1, Player $clone2){


            if ($are_clones) {
                $clone_char = new Player($clone_1_id);
                $clone_char_2 = new Player($clone_2_id);
                $clone_char_health = $clone_char->health();
                $clone_char_2_health = $clone_char_2->health();
                $clone_char_turns = $clone_char->turns();
                $clone_char_2_turns = $clone_char_2->turns();
                $clone_char->death();
                $clone_char->changeTurns(-1*$clone_char->turns());
                $clone_char_2->death();
                $clone_char_2->changeTurns(-1*$clone_char_2->turns());
                $generic_skill_result_message = "You obliterate the clone {$clone_char->name()} for $clone_char_health health, $clone_char_turns turns
                     and the clone {$clone_char_2->name()} for $clone_char_2_health health, $clone_char_2_turns turns.";
                send_event($char_id, $clone_1_id, "You and {$clone_char_2->name()} were Clone Killed at $today.");
                send_event($char_id, $clone_2_id, "You and {$clone_char->name()} were Clone Killed at $today.");
            } else {
                $generic_skill_result_message = "Those two ninja don't seem to be clones.";
            }
    }
}