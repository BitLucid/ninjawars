<?php
use NinjaWars\core\data\Item;
use NinjaWars\core\data\DatabaseConnection;

// FUNCTIONS

function getItemByID($p_itemID) {
	return buildItem(item_info($p_itemID));
}

// Wrapper that creates an object when given the row data.
function buildItem($p_data) {
	$item = null;

	if ($p_data) {
		$item = new Item();
		$item->buildFromArray($p_data);
	}

	return $item;
}

// Return a specific bit of info about an item, or else all the info about an item.
function item_info($item_id, $specific=null) {
	$info = query_row('SELECT item_id, item_internal_name, item_display_name, item_cost, image, for_sale, usage, 
			ignore_stealth, covert, turn_cost, target_damage, turn_change, self_use, other_usable, plural 
			FROM item WHERE item_id = :item_id', array(':item_id'=>array($item_id, PDO::PARAM_INT)));

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

// Pull the gold a user has.
function get_gold($char_id){
	return (int) query_item('SELECT gold FROM players WHERE player_id = :char_id', array(':char_id'=>$char_id));
}

// Add to the gold of a user.
function add_gold($char_id, $amount){
	$amount = (int)$amount;
	if ($amount != 0) { // Only update anything if it's a non-null non-zero value.
		query('UPDATE players SET 
			gold = gold + CASE WHEN gold + :amount1 < 0 THEN gold*(-1) ELSE :amount2 END where player_id = :char_id', 
				array(':char_id'=>$char_id, ':amount1'=>array($amount, PDO::PARAM_INT), ':amount2'=>array($amount, PDO::PARAM_INT)));
	}
	return get_gold($char_id);
}

// Negative brother of the add_gold function.
function subtract_gold($char_id, $amount){
	return add_gold($char_id, $amount*-1);
}

// DEPRECATED
// Add an item using it's database identity.
function add_item($char_id, $identity, $quantity = 1) {
	$quantity = (int)$quantity;
	if ($quantity > 0 && !empty($identity)) {
	    $up_res = query_resultset(
	        "UPDATE inventory SET amount = amount + :quantity
	            WHERE owner = :char AND item_type = (select item_id from item where item_internal_name = :identity)",
	        array(':quantity'=>$quantity,
	            ':char'=>$char_id,
	            ':identity'=>$identity));
	    $rows = $up_res->rowCount();

		if (!$rows) { // No entry was present, insert one.
		    query_resultset("INSERT INTO inventory (owner, item_type, amount)
		        VALUES (:char, (SELECT item_id FROM item WHERE item_internal_name = :identity), :quantity)",
		        array(':char'=>$char_id,
		            ':identity'=>$identity,
		            ':quantity'=>$quantity));
		}
	} else {
	    throw new \Exception('Invalid item to add to inventory.');
	}
}

function removeItem($who, $item, $quantity=1) {
	DatabaseConnection::getInstance();
	$statement = DatabaseConnection::$pdo->prepare("UPDATE inventory SET amount = greatest(0, amount - :quantity) WHERE owner = :user AND item_type = (SELECT item_id FROM item WHERE lower(item_display_name) = lower(:item)) AND amount > 0");
	$statement->bindValue(':user', $who);
	$statement->bindValue(':item', $item);
	$statement->bindValue(':quantity', $quantity);
	$statement->execute();
}
