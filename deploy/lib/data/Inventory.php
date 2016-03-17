<?php
namespace NinjaWars\core\data;

use NinjaWars\core\data\Player;
use \PDO;

/**
 * Inventory for characters
 */
class Inventory {
    /**
     * Get inventory list of a character
     */
    public static function listing(Player $ch, $sort=null){
        if($sort==='self'){
            $order = "ORDER BY self_usage DESC, item_display_name";
        } else {
            $order = "ORDER BY item_internal_name = 'shuriken' DESC, item_display_name";
        } 

        $sql = "SELECT amount AS count, item_display_name AS name, item_type, item.item_id, other_usable
            FROM inventory join item on item_type = item.item_id
            WHERE owner = :owner ".$order;
        return query($sql, array(':owner'=>array($ch->id(), PDO::PARAM_INT)));
    }
}
