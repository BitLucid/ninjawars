<?php
use NinjaWars\core\data\DatabaseConnection;
use NinjaWars\core\data\ClanFactory;

require_once(LIB_ROOT."control/lib_status.php");
require_once(LIB_ROOT."control/lib_accounts.php");

/**
 * Categorize ninja ranks by level.
 */
function level_category($level) {
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
 * The number of kills needed to level up to the next level.
 */
function required_kills_to_level($current_level) {
	$levelling_cost_multiplier = 5; // 5 more kills in cost for every level you go up.
	$required_kills = ($current_level)*$levelling_cost_multiplier;
	return $required_kills;
}

/**
 * Leveling up Function
 */
function level_up_if_possible($char_id) {
	// Setup values:
	$max_level = MAX_PLAYER_LEVEL;
	$health_to_add = 100;
	$turns_to_give = 50;
	$stat_value_to_add = 5;

	$char_kills = get_kills($char_id);

	if ($char_kills < 0) {
		// If the character doesn't have any kills, shortcut the levelling process.
		return false;
	} else {
		$char_obj = new Player($char_id);
		$char_level = $char_obj->level();

		if ($char_obj->isAdmin()) {
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
				$userLevel = changeLevel($char_id, 1);
				change_strength($char_id, $stat_value_to_add);
				change_speed($char_id, $stat_value_to_add);
				change_stamina($char_id, $stat_value_to_add);
				change_karma($char_id, 1); // Only add 1 to karma via levelling.
				change_ki($char_id, 50); // Add 50 ki points via levelling.
				changeHealth($char_id, $health_to_add);
				change_turns($char_id, $turns_to_give);
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
 * Check that a class matches against the class identities available in the database.
 */
function is_valid_class($candidate_identity) {
    return (boolean) query_item(
        "SELECT identity FROM class WHERE identity = :candidate",
        [':candidate'=>$candidate_identity]
    );
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
	return (($clan = ClanFactory::clanOfMember($player_id)) && $player_id == $clan->getLeaderID());
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
function add_data_to_player_row($player_data) {
    unset($player_data['pname']);

    $player_data['max_health']    = max_health_by_level($player_data['level']);
	$player_data['hp_percent']    = min(100, round(($player_data['health']/$player_data['max_health'])*100));
	$player_data['max_turns']     = 100;
	$player_data['turns_percent'] = min(100, round($player_data['turns']/$player_data['max_turns']*100));
	$player_data['next_level']    = required_kills_to_level($player_data['level']);
	$player_data['exp_percent']   = min(100, round(($player_data['kills']/$player_data['next_level'])*100));
	$player_data['status_list']   = implode(', ', get_status_list($player_data['player_id']));
	$player_data['hash']          = md5(implode($player_data));

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
 * @param int $p_id
 */
function char_info($p_id) {
		if($p_id === null){
			throw new \InvalidArgumentException('Call to char_info with no valid player_id argument.');
		}

		if(!is_numeric($p_id) || !positive_int($p_id)){
			return null; // p_id must be positive & numeric.
		}
		$player = new Player($p_id); // Constructor uses DAO to get player object.
		$player_data = array();

		if ($player instanceof Player && $player->id()) {
			// Turn the player data vo into a simple array.
			$player_data = (array) $player->vo;
			$player_data['clan_id'] = ($player->getClan() ? $player->getClan()->getID() : null);
			$player_data = add_data_to_player_row($player_data);
		}

		return $player_data;
}

function changeLevel($who, $amount) {
	$amount = (int)$amount;
	if (abs($amount) > 0) {
		DatabaseConnection::getInstance();

		$statement = DatabaseConnection::$pdo->prepare("UPDATE players SET level = level+:amount WHERE player_id = :player");
		$statement->bindValue(':player', $who);
		$statement->bindValue(':amount', $amount);
		$statement->execute();

		// *** UPDATE THE LEVEL INCREASE LOG *** //
		$statement = DatabaseConnection::$pdo->prepare("SELECT * FROM levelling_log WHERE _player_id = :player AND killsdate = now()");
		$statement->bindValue(':player', $who);
		$statement->execute();

		$notYetANewDay = $statement->fetch();  //Throws back a row result if there is a pre-existing record.

		if ($notYetANewDay != NULL) {
			//if record already exists.
			$statement = DatabaseConnection::$pdo->prepare("UPDATE levelling_log SET levelling=levelling + :amount WHERE _player_id = :player AND killsdate=now() LIMIT 1");
			$statement->bindValue(':amount', $amount);
			$statement->bindValue(':player', $who);
		} else {	// if no prior record exists, create a new one.
			$statement = DatabaseConnection::$pdo->prepare("INSERT INTO levelling_log (_player_id, killpoints, levelling, killsdate) VALUES (:player, '0', :amount, now())");  //inserts all except the autoincrement ones
			$statement->bindValue(':amount', $amount);
			$statement->bindValue(':player', $who);
		}

		$statement->execute();
	}

	return getLevel($who);
}

// Takes in a character id and adds kills to that character.
function addKills($who, $amount) {
    $amount = (int)abs($amount);
    update_levelling_log($who, $amount);
    return change_kills($who, $amount);
}

function subtractKills($who, $amount) {
    $amount = (int)abs($amount);
    update_levelling_log($who, -1*($amount));
    return change_kills($who, -1*($amount));
}

function get_kills($char_id) {
    return query_item(
        "SELECT kills FROM players WHERE player_id = :player_id",
        [
            ':player_id' => [$char_id, PDO::PARAM_INT]
        ]
    );
}

// Change the kills amount of a char, and levels them up when necessary.
function change_kills($char_id, $amount, $auto_level_check=true) {
    $amount = (int)$amount;

    if (abs($amount) > 0) {
        // Ignore changes that amount to zero.
        if ($amount > 0 && $auto_level_check) {
            // For positive kill changes, check whether levelling occurs.
            level_up_if_possible($char_id);
        }

        $query = <<<EOT
UPDATE players
SET kills = kills +
CASE WHEN kills + :amount1 < 0 THEN kills*(-1) ELSE :amount2 END
WHERE player_id = :player_id
EOT;

        query($query,
            [
                ':amount1'   => [$amount, PDO::PARAM_INT],
                ':amount2'   => [$amount, PDO::PARAM_INT],
                ':player_id' => $char_id
            ]
        );
    }

    return get_kills($char_id);
}

// Update the levelling log with the increased kills.
function update_levelling_log($who, $amount) {
    // TODO: This should be deprecated once we have only upwards kills_total increases, but for now I'm just refactoring.
    DatabaseConnection::getInstance();

    $amount = (int)$amount;

    if ($amount == 0) {
        return;
    } else if ($amount > 0) {
        $record_check = '>';
    } else {
        $record_check = '<';
    }

    // *** UPDATE THE KILLS LOG ***
    $statement = DatabaseConnection::$pdo->prepare(
        "SELECT * FROM levelling_log WHERE _player_id = :player AND killsdate = now() AND killpoints $record_check 0 LIMIT 1");
    //Check for an existing record of either negative or positive types.
    $statement->bindValue(':player', $who);
    $statement->execute();

    $notYetANewDay = $statement->fetch();  //positive if todays record already exists
    if ($notYetANewDay != NULL) {
        // If an entry already exists, update it.
        $statement = DatabaseConnection::$pdo->prepare("UPDATE levelling_log SET killpoints = killpoints + :amount WHERE _player_id = :player AND killsdate = now() AND killpoints $record_check 0");  //increase killpoints
    } else {
        $statement = DatabaseConnection::$pdo->prepare(
            "INSERT INTO levelling_log (_player_id, killpoints, levelling, killsdate) VALUES (:player, :amount, '0', now())");
        //create a new record for today
    }

    $statement->bindValue(':amount', $amount);
    $statement->bindValue(':player', $who);
    $statement->execute();
}
