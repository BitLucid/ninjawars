<?php
require_once(LIB_ROOT."control/lib_status.php");
require_once(LIB_ROOT."control/lib_accounts.php");
// lib_player.php

// Define for GRAVATAR OPTIONS moved to the tracked constant file.

/***********************   Refactor these class functions from commands.php  ********************************/


// ************************************
// ********** CLASS FUNCTIONS *********
// ************************************

// Wrapper functions for the old usages.
// DEPRECATED
function setClass($who, $new_class) {
	$char_id = get_char_id($who);
	return set_class($char_id, $new_class);
}

// Wrapper functions for the old usages.
// DEPRECATED
function getClass($who) {
	$char_id = get_char_id($who);
	return char_class_identity($char_id);
	// Note that classes now have identity/name(for display)/theme, so this function should be deprecated.
}


	// ************************************
	// ************************************


// Centralized holding for the maximum level available in the game.
function maximum_level() {
	return 250;
}

// Get a character's level, necessary when a character's level gets changed.
function char_level($char_id) {
	$info = get_player_info($char_id);
	return $info['level'];
}

// The number of kills needed to level up to the next level.
function required_kills_to_level($current_level) {
	$levelling_cost_multiplier = 5; // 5 more kills in cost for every level you go up.
	$required_kills = ($current_level)*$levelling_cost_multiplier;
	return $required_kills;

}

// Get a character's current kills, necessary when a character's level changes.
function char_kills($char_id) {
	$info = get_player_info($char_id);
	return $info['kills'];
}


// ******** Leveling up Function *************************
// Incorporate this into the kill system to cause auto-levelling.
function level_up_if_possible($char_id) {
	// Setup values:
	$max_level = maximum_level();
	$health_to_add = 100;
	$turns_to_give = 50;
	$stat_value_to_add = 5;



	$username = get_char_name($char_id);
	$char_level = getLevel($username);
	$char_kills = getKills($username);


	// Check required values:
	$nextLevel  = $char_level + 1;
	$required_kills = required_kills_to_level($char_level);
	// Have to be under the max level and have enough kills.
	$level_up_possible = (
		($nextLevel < $max_level) &&
		($char_kills >= $required_kills) );

	if ($level_up_possible) {
		// ****** Perform the level up actions ****** //
		$userKills = subtractKills($username, $required_kills);
		$userLevel = addLevel($username, 1);
		change_strength($char_id, $stat_value_to_add);
		change_speed($char_id, $stat_value_to_add);
		change_stamina($char_id, $stat_value_to_add);
		change_karma($char_id, 1); // Only add 1 to karma via levelling.
		change_ki($char_id, 50); // Add 50 ki points via levelling.
		addHealth($username, $health_to_add);
		addTurns($username, $turns_to_give);
		return true;
	} else {
		return false;
	}
}

// ************************************
// ********* STAT changing functions *******
// ************************************

function change_strength($char_id, $amount){
	$amount = (int) $amount;
	if(abs($amount) > 0){
		$up = "UPDATE players set strength = (strength+:amount) where player_id = :player_id";
		query($up, array(':amount'=>$amount, ':player_id'=>array($char_id, PDO::PARAM_INT)));
	}
}


function change_speed($char_id, $amount){
	$amount = (int) $amount;
	if(abs($amount) > 0){
		$up = "UPDATE players set speed = (speed+:amount) where player_id = :player_id";
		query($up, array(':amount'=>$amount, ':player_id'=>array($char_id, PDO::PARAM_INT)));
	}
}


function change_stamina($char_id, $amount){
	$amount = (int) $amount;
	if(abs($amount) > 0){
		$up = "UPDATE players set stamina = (stamina+:amount) where player_id = :player_id";
		query($up, array(':amount'=>$amount, ':player_id'=>array($char_id, PDO::PARAM_INT)));
	}
}

function change_karma($char_id, $amount){
	$amount = (int) $amount;
	if(abs($amount) > 0){
		$up = "UPDATE players set karma = (karma+:amount) where player_id = :player_id";
		query($up, array(':amount'=>$amount, ':player_id'=>array($char_id, PDO::PARAM_INT)));
		// Change the total karma tracked in the account at the same time as the character karma changes.
		$up2 = "UPDATE accounts set karma_total = (karma_total+:amount) where account_id = 
			(select _account_id from account_players where _player_id = :player_id)";
		query($up, array(':amount'=>$amount, ':player_id'=>array($char_id, PDO::PARAM_INT)));
	}
}

function change_ki($char_id, $amount){
	$amount = (int) $amount;
	if(abs($amount) > 0){
		$up = "UPDATE players set ki = (ki+:amount) where player_id = :player_id";
		query($up, array(':amount'=>$amount, ':player_id'=>array($char_id, PDO::PARAM_INT)));
	}
}


// Check that a class matches against the class identities available in the database.
function is_valid_class($potential_class_identity) {
	$sel = "select identity from class";
	$classes = query_array($sel);
	foreach ($classes as $l_class) {
		if ($l_class['identity'] == $potential_class_identity) {
			return true;
		}
	}
	return false;
}

// Set the character's class, using the identity.
function set_class($char_id, $new_class) {
	if (!is_valid_class(strtolower($new_class))) {
		return "That class was not an option to change into.";
	} else {
		$up = "UPDATE players SET _class_id = (select class_id FROM class WHERE class.identity = :class) WHERE player_id = :char_id";
		query($up, array(':class'=>strtolower($new_class), ':char_id'=>$char_id));

		return null;
	}
}


// Get the character class display name info.
function char_class_name($char_id) {
	return query_item("SELECT class.class_name FROM players JOIN class ON class_id = _class_id WHERE player_id = :char_id",
		array(':char_id'=>$char_id));
}


// Get the character class information.
function char_class_identity($char_id) {
	return query_item("SELECT class.identity FROM players JOIN class ON class_id = _class_id WHERE player_id = :char_id",
		array(':char_id'=>$char_id));
}


// Get the character class theme string.
function char_class_theme($char_id) {
	return query_item("SELECT class.theme FROM players JOIN class ON class_id = _class_id WHERE player_id = :char_id",
		array(':char_id'=>$char_id));
}

// Pull the class theme by identity.
function class_theme($class_identity) {
	return query_item('SELECT theme FROM class WHERE identity = :class_identity',
		array(':class_identity'=>$class_identity));
}

/**
 * Pull out the url for the player's avatar
**/
function create_avatar_url($player, $size=null) {
	// If the avatar_type is 0, return '';
    if (!$player->vo || !$player->vo->avatar_type || !$player->email()) {
        return '';
    } else {	// Otherwise, user the player info for creating a gravatar.
		$email       = $player->email();
		$avatar_type = $player->vo->avatar_type;
		return create_gravatar_url_from_email($email, $avatar_type, $size);
	}
}

function generate_gravatar_url($player) {
	if (!is_object($player)) {
		$player = new Player($player);
	}

	return (OFFLINE ? IMAGE_ROOT.'default_avatar.png' : create_avatar_url($player));
}

// Use the email information to return the gravatar image url.
function create_gravatar_url_from_email($email, $avatar_type=null, $size=null) {
	$def         = 'monsterid'; // Default image or image class.
	// other options: wavatar (polygonal creature) , monsterid, identicon (random shape)
	$base        = "http://www.gravatar.com/avatar/";
	$hash        = md5(trim(strtolower($email)));
	$no_gravatar = "d=".urlencode($def);
	$size        = whichever($size, 80);
	$rating      = "r=x";
	$res         = $base.$hash."?".implode('&', array($no_gravatar, $size, $rating));

	return $res;
}

// *** Get list of clan members from clan ***
/// TODO - Should be moved to clan stuff
function get_clan_members($p_clanID, $p_limit = 30) {
	if ((int)$p_clanID == $p_clanID && $p_clanID > 0) {
		$sel = "SELECT uname, player_id, health FROM clan_player JOIN players ON player_id = _player_id AND _clan_id = :clanID AND active = 1 ORDER BY health DESC, level DESC ".(!is_null($p_limit) && $p_limit > 0 ? "LIMIT :limit" : '');
		DatabaseConnection::getInstance();
		$statement = DatabaseConnection::$pdo->prepare($sel);
		$statement->bindValue(':clanID', $p_clanID);

		if (!is_null($p_limit) && $p_limit > 0) {
			$statement->bindValue(':limit', $p_limit);
		}

		$statement->execute();

		return $statement;
	} else {
		return null;
	}
}

/**
 * Runs inventory query on a character id
**/
function getInventory($p_characterID) {
	$sel = "SELECT owner, item_internal_name, item_display_name, item.item_id, amount
		FROM inventory JOIN item on inventory.item_type = item.item_id
		WHERE owner = :owner_id
		AND amount > 0 ORDER BY item_internal_name = 'shuriken' DESC, item_display_name";
	return query($sel, array(':owner_id'=>array((int)$p_characterID, PDO::PARAM_INT)));
}

// Check whether the player is the leader of their clan.
function is_clan_leader($player_id) {
	return (($clan = get_clan_by_player_id($player_id)) && $player_id == get_clan_leader_id($clan->getID()));
}

// Get the rank integer for a certain character.
function get_rank($username) {
	DatabaseConnection::getInstance();
	$statement = DatabaseConnection::$pdo->prepare("SELECT rank_id FROM rankings WHERE uname = :player");
	$statement->bindValue(':player', $username);
	$statement->execute();

	$rank = $statement->fetchColumn();

	return ($rank > 0 ? $rank : 1); // Make rank default to 1 if no valid ones are found.
}

// Return the current percentage of the maximum health that a character could have.
function health_percent($health, $level) {
	return min(100, round(($health/determine_max_health($level))*100));
}

// Format a player data row with health and level and add the data for a health percentage.
function format_health_percent($player_row) {
	$percent = health_percent($player_row['health'], $player_row['level']);
	$player_row['health_percent'] = $percent;
	return $player_row;
}
?>
