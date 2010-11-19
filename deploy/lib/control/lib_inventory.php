<?php
// lib_inventory.php

// FUNCTIONS

function items_info($item_id=null) {
	return query('select * from item');
}

function getItemByID($p_itemID) {
	return buildItem(item_info($p_itemID));
}

function getItemByIdentity($p_itemIdentity) {
	return buildItem(item_info_from_identity($p_itemIdentity));
}

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
	$info = query_row('SELECT item_id, item_internal_name, item_display_name, item_cost, image, for_sale, usage, ignore_stealth, covert, turn_cost, target_damage, turn_change, self_use, plural FROM item WHERE item_id = :item_id', array(':item_id'=>array($item_id, PDO::PARAM_INT)));

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

	return item_info($item_id, $specific);
}

// Wrapper functions to return certain aspects of items.

function item_identity($item_id){
	return item_info($item_id, 'item_internal_name');
}

function item_display_name($item_id){
	return item_info($item_id, 'item_display_name');
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
	$statement = query("SELECT sum(amount) FROM inventory join item on inventory.item_type = item.item_id WHERE owner = :owner AND lower(item_display_name) = lower(:item)", array(':owner'=>array($user_id, PDO::PARAM_INT), ':item'=>strtolower($item_display_name)));
	return $statement->fetchColumn();
}

// Pull the counts of all items a player has.
function inventory_counts($char_id){
	$sql = "SELECT amount AS count, item_display_name AS name, item_type FROM inventory join item on item_type = item.item_id WHERE owner = :owner";
	return query_resultset($sql, array(':owner'=>array($char_id, PDO::PARAM_INT)));
}

// Pulls the shop items costs and all.
function item_for_sale_costs(){
	$sel = 'select item_display_name, item_internal_name, item_cost, image, usage from item where for_sale = TRUE';
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
	if (abs($amount) >  0) { // Only update anything if it's a non-null non-zero value.
		query('UPDATE players SET 
			gold = gold + CASE WHEN gold + :amount < 0 THEN gold*(-1) ELSE :amount END where player_id = :char_id', 
				array(':char_id'=>$char_id, ':amount'=>$amount));
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
		$turns_decrease = rand(1,11)+$near_level_power_increase; // *** 1-11 + 0-10
	} elseif ($targets_turns>10) {
		$turns_decrease = rand(1, 5)+$near_level_power_increase;
	} elseif ($targets_turns>2) {
		$turns_decrease = rand(1, 2)+($near_level_power_increase? 1 : 0);
	} else { // *** Players are always left with 1 or two turns.
		$turns_decrease = '0';
	} // End of turn checks.
	return $turns_decrease;
}


// Send out the killed messages.
function send_kill_mails($username, $target, $attacker_id, $article, $item, $today, $loot){
	$target_email_msg   = "You have been killed by $attacker_id with $article $item at $today and lost $loot gold.";
	sendMessage($attacker_id,$target,$target_email_msg);

	$user_email_msg     = "You have killed $target with $article $item at $today and received $loot gold.";
	sendMessage($target,$username,$user_email_msg);
}

// Item data for the inventory.
function standard_items() {
	// Codename means it can have a link to be used, apparently...
	// Pull this from the database.
	$it = items_info();

	$res = array();
	// Format the items for display on the inventory.
	foreach($it as $item){
		$item['codename'] = $item['item_display_name'];
		$item['display'] = $item['item_display_name'].$item['plural'];
		$res[$item['item_id']] = $item;
	}
	return $res;
}



// END OF FUNCTIONS



// This could probably be moved to some lib file for use in different places.
class Item {
	protected $m_name;
	protected $m_plural;
	protected $m_ignoresStealth;
	protected $m_targetDamage;
	protected $m_maxDamage;
	protected $m_turnCost;
	protected $m_turnChange;
	protected $m_maxTurnChange;
	protected $m_covert;
	protected $m_selfUse;
	protected $m_type;
	protected $m_identity;

	/*
	public function __construct($p_name) {
		$this->m_ignoresStealth = false;
		$this->m_name = trim($p_name);
		$this->m_turnCost = 1;
		$this->m_turnChange = null;
		$this->m_type = item_id_from_display_name($p_name);
	}*/

	// Set all the default settings for items, overridden by specified settings.
	public function __construct() {
	}

	public function buildFromArray($p_data) {
		$this->m_type              = $p_data['item_id'];
		$this->m_identity          = $p_data['item_internal_name'];
		$this->m_name              = $p_data['item_display_name'];
		$this->m_plural            = $p_data['plural'];
		$this->m_turnCost          = ($p_data['turn_cost']     ? $p_data['turn_cost']     : 1);
		$this->m_maxTurnChange     = ($p_data['turn_change']   ? $p_data['turn_change']   : 0);
		$this->m_maxDamage         = ($p_data['target_damage'] ? $p_data['target_damage'] : null);
		$this->m_ignop_dataStealth = ($p_data['ignore_stealth'] == 't');
		$this->m_covert            = ($p_data['covert']         == 't');
		$this->m_selfUse           = ($p_data['self_use']       == 't');
	}

	public function getName()
	{ return $this->m_name; }

	// Convenience function to get the plural name for the object.
	public function getPluralName()
	{ return $this->m_name.$this->m_plural; }

	public function __toString()
	{ return $this->getName(); }

	public function id()
	{ return $this->m_type; }

	// The item's internally used name.
	public function identity()
	{ return $this->m_identity; }

	// Gets the list of effects that the item does.
	public function effects() {
		$sel = 'SELECT effect_identity, effect_name, effect_verb, effect_self FROM effects
		    JOIN item_effects ON _effect_id = effect_id WHERE _item_id = :item_id';
		$data = query_array($sel, array(':item_id' => array((int)$this->id(), PDO::PARAM_INT)));
		$res = array();

		foreach ($data as $effect) {
			$res[$effect['effect_identity']] = $effect;
		}

		return $res;
	}

	// Checks whether the item causes a certain effect.
	function hasEffect($effect_identity)
	{ return (array_key_exists($effect_identity, $this->effects())); }

	public function setIgnoresStealth($p_ignore)
	{ $this->m_ignoresStealth = (boolean)$p_ignore; }

	public function ignoresStealth()
	{ return $this->m_ignoresStealth; }

	public function setTargetDamage($p_damage)
	{ $this->m_targetDamage = (float)$p_damage; }

	public function getTargetDamage()
	{ return $this->m_targetDamage; }

	public function getTurnCost()
	{ return $this->m_turnCost; }

	// Note that this just determines the -maximum- turn change.
	public function setTurnChange($p_turns)
	{ $this->m_turnChange = (float)$p_turns; }

	public function getTurnChange()
	{ return $this->m_turnChange; }

	public function getMaxTurnChange()
	{ return $this->m_maxTurnChange; }

	public function setCovert($p_covert)
	{ $this->m_covert = (boolean)$p_covert; }

	public function isCovert()
	{ return $this->m_covert; }

	public function isSelfUsable()
	{ return $this->m_selfUse;	}

	public function getType()
	{ return $this->m_type; }
}
// Default could be an error later.
?>
