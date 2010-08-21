<?php
// lib_inventory.php


// FUNCTIONS

// 
function item_identity($item_id){
    return query_item('select item_internal_name from item where item_id = :item_id', array(':item_id'=>array($item_id, PDO::PARAM_INT)));    
}

function item_id_from_display_name($item_display_name){
    return query_item('select item_id from item where item_display_name = :item_display_name', array(':item_display_name'=>
        array($item_display_name, PDO::PARAM_INT)));    
}

function item_display_name($item_id){
    return query_item('select item_display_name from item where item_id = :item_id', array(':item_id'=>array($item_id, PDO::PARAM_INT)));
}

// Get the item internal identity and display name.
function item_info($item_id){
    $sel = 'select item_display_name, item_internal_name, item_cost from item where item_id = :item_id';
    return query_array($sel, array(':item_id'=>array($item_id, PDO::PARAM_INT)));
}

// Get an input display name and turn it into the internal name for use in the actual script.
function item_internal_from_display($item_display_name){
    $sel = 'select item_internal_name from item where item_display_name = :item_display_name';
    return query_item($sel, array(':item_display_name'=>$item_display_name));    
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

// TODO: This should get the costs for each of the items from the database.
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
function render_give_item($username, $target, $item){
	$article = get_indefinite_article($item);
    addItem($target,$item,1);
    $give_msg = "You have been given $article $item by $username.";
    sendMessage($username,$target,$give_msg);
    return "$target will receive your $item.<br>\n";
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
	$sel = 'select * from item';
	$it = query($sel);
	
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
	protected $m_ignoresStealth;
	protected $m_targetDamage;
	protected $m_turnCost;
	protected $m_turnChange;
	protected $m_covert;
	protected $m_type;

	public function __construct($p_name) {
		$this->m_ignoresStealth = false;
		$this->m_name = trim($p_name);
		$this->m_turnCost = 1;
		$this->m_turnChange = null;
		$this->m_type = item_id_from_display_name($p_name);
	}

	public function getName()
	{ return $this->m_name; }

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

	public function setTurnChange($p_turns)
	{ $this->m_turnChange = (float)$p_turns; }

	public function getTurnChange()
	{ return $this->m_turnChange; }

	public function setCovert($p_covert)
	{ $this->m_covert = (boolean)$p_covert; }

	public function isCovert()
	{ return $this->m_covert; }
	
	public function getType()
	{ return $this->m_type; }
}
// Default could be an error later.


?>
