<?php
// lib_inventory.php

require_once(ROOT.'core/data/Item.php'); // Require the item object.

// FUNCTIONS

function getItemByID($p_itemID) {
	return buildItem(item_info($p_itemID));
}

// Get an item by it's item identity
function getItemByIdentity($p_itemIdentity) {
	
	return buildItem(item_info_from_identity($p_itemIdentity));
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

// Get an input display name and turn it into the internal name for use in the actual script.
function item_identity_from_display_name($item_display_name){
	return item_info(item_id_from_display_name($item_display_name), 'item_internal_name');
}


// Get the count of how many of an item a player has.
function item_count($user_id, $item_display_name){
	$statement = query("SELECT sum(amount) FROM inventory join item on inventory.item_type = item.item_id 
		WHERE owner = :owner AND lower(item_display_name) = lower(:item)", 
		array(':owner'=>array($user_id, PDO::PARAM_INT), ':item'=>strtolower($item_display_name)));
	return $statement->fetchColumn();
}

// Pull the counts of all items a player has.
function inventory_counts($char_id){
	$sql = "SELECT amount AS count, item_display_name AS name, item_type, item.item_id, other_usable
		FROM inventory join item on item_type = item.item_id
		WHERE owner = :owner ORDER BY item_internal_name = 'shuriken' DESC, item_display_name";
	return query_array($sql, array(':owner'=>array($char_id, PDO::PARAM_INT)));
}

// Pulls the shop items costs and all.
function item_for_sale_costs(){
	$sel = 'select item_display_name, item_internal_name, item_cost, image, usage from item where for_sale = TRUE order by image asc, item_cost asc';
	if(defined('DEBUG') && DEBUG){
		$sel = 'select item_display_name, item_internal_name, item_cost, image, usage from item order by image asc, item_cost asc';
	}
	$items_data = query_resultset($sel);
	// Rearrange the array to use the internal identity as indexes.
	$item_costs = array();
	foreach($items_data as $item_data){
		$item_costs[$item_data['item_internal_name']] = $item_data;
	}
	return $item_costs;
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

// Benefits for near-equivalent levels.
function nearLevelPowerIncrease($level_difference, $max_increase) {
	$res = 0;
	$coeff = abs($level_difference);
	if ($coeff<$max_increase) {
		$res = $max_increase-$coeff;
	}
	return $res;
}


// Give the item and return a message to show the user.
function give_item($username, $target, $item){
	$article = get_indefinite_article($item);
    addItem($target,$item,1);
    $give_msg = "You have been given $article $item by $username.";
    sendMessage($username,$target,$give_msg);
}

// Determine the turns for caltrops, which was once ice scrolls.
function caltrop_turn_loss($targets_turns, $near_level_power_increase){
	if ($targets_turns>50) {
		$turns_decrease = rand(1,8)+$near_level_power_increase; // *** 1-11 + 0-10
	} elseif ($targets_turns>20) {
		$turns_decrease = rand(1, 5)+$near_level_power_increase;
	} elseif ($targets_turns>3) {
		$turns_decrease = rand(1, 2)+($near_level_power_increase? 1 : 0);
	} else { // *** Players are always left with 1 or two turns.
		$turns_decrease = 0;
	} // End of turn checks.
	return $turns_decrease;
}


// Send out the killed messages.
function send_kill_mails($username, $target, $attacker_id, $article, $item, $loot) {
	$target_email_msg   = "You have been killed by $attacker_id with $article $item and lost $loot gold.";
	sendMessage($attacker_id,$target,$target_email_msg);

	$user_email_msg     = "You have killed $target with $article $item and received $loot gold.";
	sendMessage($target,$username,$user_email_msg);
}

// Item data for the inventory.
function standard_items() {
	// Codename means it can have a link to be used, apparently...
	// Pull this from the database.
	$it = query('select * from item');

	$res = array();
	// Format the items for display on the inventory.
	foreach($it as $item){
		$item['codename'] = $item['item_display_name'];
		$item['display'] = $item['item_display_name'].$item['plural'];
		$res[$item['item_id']] = $item;
	}

	return $res;
}

// Pull an item's effects.
function item_effects($item_id){
	$sel = 'SELECT effect_identity, effect_name, effect_verb, effect_self FROM effects
		    JOIN item_effects ON _effect_id = effect_id WHERE _item_id = :item_id';
		$data = query_array($sel, array(':item_id' => array($item_id, PDO::PARAM_INT)));
		$res = array();
		foreach ($data as $effect) {
			$res[strtolower($effect['effect_identity'])] = $effect;
		}
		return $res;
}


// END OF FUNCTIONS




// Default could be an error later.
