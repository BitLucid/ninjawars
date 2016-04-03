<?php
use NinjaWars\core\data\Item;
use NinjaWars\core\data\Inventory;
use NinjaWars\core\data\Player;
use NinjaWars\core\data\DatabaseConnection;

// Return a specific bit of info about an item, or else all the info about an item.
function item_info($item_id, $specific=null) {
	$info = query_row('SELECT item_id, item_internal_name, item_display_name, item_cost, image, for_sale, usage, 
			ignore_stealth, covert, turn_cost, target_damage, turn_change, self_use, other_usable, plural 
			FROM item WHERE item_id = :item_id', [':item_id'=>$item_id]);

	if (!$info || ($specific && !isset($info[$specific]))) {
		return null;
	} elseif ($specific) {
		return $info[$specific];
	} else {
		return $info;
	}
}

// Find all the info or just a piece from the item's identity.
function item_info_from_identity($identity, $specific=null) {
	$item_id = query_item('SELECT item_id FROM item WHERE item_internal_name = :identity', array(':identity'=>$identity));
	// Uses the item_info function once the item_id is determined.
	return item_info($item_id, $specific);
}

// Necessary reversal function for older uses of display names in the code.
function item_id_from_display_name($item_display_name){
	return query_item('select item_id from item where item_display_name = :item_display_name', array(':item_display_name'=> array($item_display_name, PDO::PARAM_INT)));
}


// Pull the counts of all items a player has.
function inventory_counts($char_id){
	$sql = "SELECT amount AS count, item_display_name AS name, item_type, item.item_id, other_usable
		FROM inventory join item on item_type = item.item_id
		WHERE owner = :owner ORDER BY item_internal_name = 'shuriken' DESC, item_display_name";
	return query_array($sql, array(':owner'=>array($char_id, PDO::PARAM_INT)));
}


/**
 * Add an item using it's database identity.
 * @deprecated
 */ 
function add_item($char_id, $identity, $quantity = 1) {
	$inventory = new Inventory(Player::find($char_id));
	$inventory->add($identity, $quantity);
}

function removeItem($who, $item, $quantity=1) {
	DatabaseConnection::getInstance();
	$statement = DatabaseConnection::$pdo->prepare("UPDATE inventory SET amount = greatest(0, amount - :quantity) WHERE owner = :user AND item_type = (SELECT item_id FROM item WHERE lower(item_display_name) = lower(:item)) AND amount > 0");
	$statement->bindValue(':user', $who);
	$statement->bindValue(':item', $item);
	$statement->bindValue(':quantity', $quantity);
	$statement->execute();
}

/*
 * Returns a comma-seperated string of states based on the statuses of the target.
 * @param array $statuses status array
 * @param string $target the target, username if self targetting.
 * @return string
 *
 */
function get_status_list($target=null) {
	$states = array();
	$target = (isset($target) && (int)$target == $target ? $target : self_char_id());

	// Default to showing own status.
	$target = Player::find($target);

	if (!$target || $target->health < 1) {
		$states[] = 'Dead';
	} else { // *** Other statuses only display if not dead.
		if ($target->health < 80) {
			$states[] = 'Injured';
		} else {
			$states[] = 'Healthy';
		}

        // The visibly viewable statuses.
		if ($target->hasStatus(STEALTH)) { $states[] = 'Stealthed'; }
		if ($target->hasStatus(POISON)) { $states[] = 'Poisoned'; }
		if ($target->hasStatus(WEAKENED)) { $states[] = 'Weakened'; }
		if ($target->hasStatus(FROZEN)) { $states[] = 'Frozen'; }
		if ($target->hasStatus(STR_UP1)) { $states[] = 'Buff'; }
		if ($target->hasStatus(STR_UP2)) { $states[] = 'Strength+'; }

		// If any of the shield skills are up, show a single status state for any.
		if ($target->hasStatus(FIRE_RESISTING) || $target->hasStatus(INSULATED) || $target->hasStatus(GROUNDED)
		    || $target->hasStatus(BLESSED) || $target->hasStatus(IMMUNIZED)
		    || $target->hasStatus(ACID_RESISTING)) {
		    $states[] = 'Shielded';
		}
	}

	return $states;
}
