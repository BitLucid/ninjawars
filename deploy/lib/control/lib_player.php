<?php
require_once(LIB_ROOT."control/lib_status.php");
require_once(LIB_ROOT."control/lib_accounts.php");

/**
 * Categorize ninja ranks by level.
 */
function level_category($level) {
	$res = '';
	switch (true) {
		case($level<2):
			$res= 'Novice';
			break;
		case($level<6):
			$res= 'Acolyte';
			break;
		case($level<31):
			$res= 'Ninja';
			break;
		case($level<51):
			$res= 'Elder Ninja';
			break;
		case($level<101):
			$res= 'Master Ninja';
			break;
		default:
			$res= 'Shadow Master';
			break;
	}

	return [
		'display' => $res,
		'css' => strtolower(str_replace(" ", "-", $res))
	];
}

/**
 * Calculate a max health by a level, will be used in dojo.php, the player object, and calculating experience.
 */
function max_health_by_level($level) {
	$health_per_level = 25;
	return 150 + round($health_per_level*($level-1));
}

/**
 * Centralized holding for the maximum level available in the game.
 */
function maximum_level() {
	return MAX_PLAYER_LEVEL;
}

/**
 * The number of kills needed to level up to the next level.
 */
function required_kills_to_level($current_level) {
	$levelling_cost_multiplier = 5; // 5 more kills in cost for every level you go up.
	$required_kills = ($current_level)*$levelling_cost_multiplier;
	return $required_kills;
}

/**
 * Get a character's current kills, necessary when a character's level changes.
 */
function char_kills($char_id) {
	$info = char_info($char_id);
	return $info['kills'];
}

/**
 * Leveling up Function
 */
function level_up_if_possible($char_id, $auto_level=false) {
	// Setup values:
	$max_level = maximum_level();
	$health_to_add = 100;
	$turns_to_give = 50;
	$stat_value_to_add = 5;

	$char_kills = get_kills($char_id);

	if($char_kills<0){
		// If the character doesn't have any kills, shortcut the levelling process.
		return false;
	} else {
		$char_obj = new Player($char_id);
		$char_level = $char_obj->level();

		if($auto_level && $char_obj->isAdmin()){
			// If the character is an admin, do not auto-level them.
			return false;
		} else {
			// For normal characters, do auto-level them.

			// Check required values:
			$nextLevel  = $char_level + 1;
			$required_kills = required_kills_to_level($char_level);
			// Have to be under the max level and have enough kills.
			$level_up_possible = (
				($nextLevel <= $max_level) &&
				($char_kills >= $required_kills) );

			if ($level_up_possible) {
				// ****** Perform the level up actions ****** //
				// Explicitly call for the special case of kill changing to prevent an infinite loop.
				change_kills($char_id, -1*$required_kills, false);
				$userLevel = addLevel($char_id, 1);
				change_strength($char_id, $stat_value_to_add);
				change_speed($char_id, $stat_value_to_add);
				change_stamina($char_id, $stat_value_to_add);
				change_karma($char_id, 1); // Only add 1 to karma via levelling.
				change_ki($char_id, 50); // Add 50 ki points via levelling.
				addHealth($char_id, $health_to_add);
				addTurns($char_id, $turns_to_give);
				// Send a level-up message, for those times when auto-levelling happens.
				send_event($char_id, $char_id,
					"You levelled up!  Your strength raised by $stat_value_to_add, speed by $stat_value_to_add, stamina by $stat_value_to_add, Karma by 1, and your Ki raised 50!  You gained some health and turns as well!  You are now a level $userLevel ninja!  Go kill some stuff.");
				return true;
			} else {
				return false;
			}
		}
	}
}

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

/**
 * Pull the information about the classes.
 */
function classes_info(){
	$classes = query('select class_id, identity, class_name, class_note, class_tier, class_desc, class_icon, theme from class where class_active = true');
	return array_identity_associate($classes, 'identity');
}

/**
 * Check that a class matches against the class identities available in the database.
 */
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

/**
 * Set the character's class, using the identity.
 */
function set_class($char_id, $new_class) {
	if (!is_valid_class(strtolower($new_class))) {
		return "That class was not an option to change into.";
	} else {
		$up = "UPDATE players SET _class_id = (select class_id FROM class WHERE class.identity = :class) WHERE player_id = :char_id";
		query($up, array(':class'=>strtolower($new_class), ':char_id'=>$char_id));

		return null;
	}
}

/**
 * Get the character class display name info.
 */
function char_class_name($char_id) {
	return query_item("SELECT class.class_name FROM players JOIN class ON class_id = _class_id WHERE player_id = :char_id",
		array(':char_id'=>$char_id));
}

/**
 * Get the character class information.
 */
function char_class_identity($char_id) {
	return query_item("SELECT class.identity FROM players JOIN class ON class_id = _class_id WHERE player_id = :char_id",
		array(':char_id'=>$char_id));
}

/**
 * Get the character class theme string.
 */
function char_class_theme($char_id) {
	return query_item("SELECT class.theme FROM players JOIN class ON class_id = _class_id WHERE player_id = :char_id",
		array(':char_id'=>$char_id));
}

/**
 * Pull the class theme by identity.
 */
function class_theme($class_identity) {
	return query_item('SELECT theme FROM class WHERE identity = :class_identity',
		array(':class_identity'=>$class_identity));
}

/**
 * Pull out the url for the player's avatar
 */
function create_avatar_url($player, $size=null) {
	// If the avatar_type is 0, return '';
    if (!$player->vo || !$player->vo->avatar_type || !$player->email()) {
        return '';
    } else {	// Otherwise, use the player info for creating a gravatar.
		$email       = $player->email();
		return create_gravatar_url_from_email($email, $size);
	}
}

/**
 */
function generate_gravatar_url($player) {
	if (!is_object($player)) {
		$player = new Player($player);
	}

	return (OFFLINE ? IMAGE_ROOT.'default_avatar.png' : create_avatar_url($player));
}

/**
 * Use the email information to return the gravatar image url.
 */
function create_gravatar_url_from_email($email, $size=null) {
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

/**
 * Get list of clan members from clan
 *
 * @TODO Should be moved to clan stuff
 */
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
 * Check whether the player is the leader of their clan.
 */
function is_clan_leader($player_id) {
	return (($clan = get_clan_by_player_id($player_id)) && $player_id == $clan->getLeaderID());
}

/**
 * Get the rank integer for a certain character.
 */
function get_rank($p_charID) {
	DatabaseConnection::getInstance();
	$statement = DatabaseConnection::$pdo->prepare("SELECT rank_id FROM rankings WHERE player_id = :player");
	$statement->bindValue(':player', $p_charID);
	$statement->execute();

	$rank = $statement->fetchColumn();

	return ($rank > 0 ? $rank : 1); // Make rank default to 1 if no valid ones are found.
}

/**
 * Return the current percentage of the maximum health that a character could have.
 */
function health_percent($health, $level) {
	return min(100, round(($health/max_health_by_level($level))*100));
}

/**
 * Format a player data row with health and level and add the data for a health percentage.
 */
function format_health_percent($player_row) {
	$percent = health_percent($player_row['health'], $player_row['level']);
	$player_row['health_percent'] = $percent;
	return $player_row;
}

/**
 * Add data to the info you get from a player row.
 */
function add_data_to_player_row($player_data, $kill_password=true){
    if($kill_password){
        unset($player_data['pname']);
    }

    $player_data['max_health'] = max_health_by_level($player_data['level']);
	$player_data['hp_percent'] = min(100, round(($player_data['health']/$player_data['max_health'])*100));
	$player_data['max_turns'] = 100;
	$player_data['turns_percent'] = min(100, round($player_data['turns']/$player_data['max_turns']*100));
	$player_data['next_level'] = required_kills_to_level($player_data['level']);
	$player_data['exp_percent'] = min(100, round(($player_data['kills']/$player_data['next_level'])*100));
	$player_data['status_list'] = implode(', ', get_status_list($player_data['player_id']));
	$player_data['hash'] = md5(implode($player_data));

	return $player_data;
}

/**
 * Return the data that should be publicly readable to javascript or the api while the player is logged in.
 */
function public_self_info(){
	$char_info = self_info();
	unset($char_info['ip'], $char_info['member'], $char_info['pname'], $char_info['pname_backup'], $char_info['verification_number'], $char_info['confirmed']);

	return $char_info;
}

/**
 * Returns the state of the current active character from the database.
 */
function self_info() {
	$id = self_char_id();
	if(!is_numeric($id)){
		// If there's no id, don't try to get any data.
		return null;
	}
	$player = new Player($id); // Constructor uses DAO to get player object.
	$player_data = array();

	if ($player instanceof Player && $player->id()) {
		// Turn the player data vo into a simple array.
		$player_data = (array) $player->vo;
		$player_data['clan_id'] = ($player->getClan() ? $player->getClan()->getID() : null);
		$player_data = add_data_to_player_row($player_data);
	}

	return $player_data;
}

/**
 * Returns the state of the player from the database,
 *
 * uses a user_id if one is present, otherwise defaults to the currently logged
 * in player, but can act on any player if another username is passed in.
 *
 * @param $user user_id or username
 * @todo consider dropping the use of whichever() inside this function
 */
function char_info($p_id) {
	if(!$p_id){
		if(defined('DEBUG') && DEBUG){
			nw_error('DEPRECATED: call to char_info with a null argument.  For clarity reasons, this is now deprecated, use the player object instead. Backtrace: '.print_r(debug_backtrace(), true));
		}
		return self_info();
	}

	$session = nw\SessionFactory::getSession();

	$id = whichever($p_id, $session->get('player_id')); // *** Default to current player. ***

	if(!is_numeric($id)){
		// If there's no id, don't try to get any data.
		return null;
	}
	$player = new Player($id); // Constructor uses DAO to get player object.
	$player_data = array();

	if ($player instanceof Player && $player->id()) {
		// Turn the player data vo into a simple array.
		$player_data = (array) $player->vo;
		$player_data['clan_id'] = ($player->getClan() ? $player->getClan()->getID() : null);
		$player_data = add_data_to_player_row($player_data);
	}

	return $player_data;
}
