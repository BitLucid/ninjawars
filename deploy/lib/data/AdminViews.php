<?php
namespace NinjaWars\core\data;

use NinjaWars\core\data\Player;
use NinjaWars\core\data\Inventory;

class AdminViews {
    public static function high_rollers(){
        // Select first few max kills from players.
        // Max turns.
        // Max gold.
        // Max kills
        // etc.
        $res = array();
        $res['gold'] = query_array('select player_id, uname, gold from players order by gold desc limit 10');
        $res['turns'] = query_array('select player_id, uname, turns from players order by turns desc limit 10');
        $res['kills'] = query_array('select player_id, uname, kills from players order by kills desc limit 10');
        $res['health'] = query_array('select player_id, uname, health from players order by health desc limit 10');
        $res['ki'] = query_array('select player_id, uname, ki from players order by ki desc limit 10');
        return $res;
    }

    public static function duped_ips(){
        $host= gethostname();
        $server_ip = gethostbyname($host);
        // Get name, id, and ip from players, grouped by ip matches
        return query('select uname, player_id, days, last_ip from players left join account_players on player_id = _player_id
            left join accounts on _account_id = account_id where uname is not null 
            and last_ip in 
            (SELECT last_ip FROM accounts 
                WHERE (operational = true and confirmed = 1) 
                    and (last_ip != \'\' and last_ip != \'127.0.0.1\' and last_ip != :server_ip) 
                GROUP  BY last_ip HAVING count(*) > 1 ORDER BY count(*) DESC limit 30)
             order by last_ip, days ASC limit 300',
             [':server_ip'=>$server_ip]);
    }


    /**
     * Reformat the character info sets.
     *
     * @return Array
     */
    public static function split_char_infos($ids) {
        $res = [];

        if (is_numeric($ids)) {
            $ids = [$ids]; // Wrap it in an array.
        } else { // Get the info for multiple ninjas
            $ids = explode(',', $ids);
        }

        $first = true;

        foreach ($ids as $id) {
            $player = Player::find($id);
            $res[$id] = $player->data();
            $res[$id]['first'] = $first;
            unset($res[$id]['messages']); // Exclude the messages for length reasons.
            unset($res[$id]['description']); // Ditto
            $first = false;
        }

        return $res;
    }

    public static function char_inventory($char_id) {
        $inventory = new Inventory(Player::find($char_id));

        return $inventory->counts();
    }
}
