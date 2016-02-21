<?php
use NinjaWars\core\data\DatabaseConnection;
use NinjaWars\core\data\ClanFactory;
use NinjaWars\core\data\AccountFactory;

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
	$health_to_add     = 100;
	$turns_to_give     = 50;
	$ki_to_give        = 50;
	$stat_value_to_add = 5;
	$karma_to_give     = 1;

    $char = new Player($char_id);

    if ($char->isAdmin()) { // If the character is an admin, do not auto-level
        return false;
    } else { // For normal characters, do auto-level
        $required_kills = required_kills_to_level($char->level());

        // Have to be under the max level and have enough kills.
        $level_up_possible = (
            ($char->level() + 1 <= MAX_PLAYER_LEVEL) &&
            ($char->kills >= $required_kills)
        );

        if ($level_up_possible) { // Perform the level up actions
            $char->set_health($char->health() + $health_to_add);
            $char->set_turns($char->turns()   + $turns_to_give);
            $char->set_ki($char->ki()         + $ki_to_give);

            // Must read from VO for these as accessors return modified values
            $char->setStamina($char->vo->stamina   + $stat_value_to_add);
            $char->setStrength($char->vo->strength + $stat_value_to_add);
            $char->setSpeed($char->vo->speed       + $stat_value_to_add);

            // no mutator for these yet
            $char->vo->kills = max(0, $char->kills - $required_kills);
            $char->vo->karma = ($char->karma() + $karma_to_give);
            $char->vo->level = ($char->level() + 1);

            $char->save();

            recordLevelUp($char->id());

            $account = AccountFactory::findByChar($char);
            $account->setKarmaTotal($account->getKarmaTotal() + $karma_to_give);
            AccountFactory::save($account);

            // Send a level-up message, for those times when auto-levelling happens.
            send_event($char->id(), $char->id(),
                "You levelled up! Your strength raised by $stat_value_to_add, speed by $stat_value_to_add, stamina by $stat_value_to_add, Karma by $karma_to_give, and your Ki raised $ki_to_give! You gained some health and turns, as well! You are now a level {$char->level()} ninja! Go kill some stuff.");
            return true;
        } else {
            return false;
        }
    }
}

/**
 * Return the current percentage of the maximum health that a character could have.
 */
function health_percent($health, $level) {
	return min(100, round(($health/Player::maxHealthByLevel($level))*100));
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
 * Return the data that should be publicly readable to javascript or the api while the player is logged in.
 */
function public_self_info() {
    $char_info = char_info(self_char_id());
    unset($char_info['ip'], $char_info['member'], $char_info['pname'], $char_info['pname_backup'], $char_info['verification_number'], $char_info['confirmed']);

    return $char_info;
}

/**
 * Returns the state of the player from the database,
 *
 * @param int $p_id
 */
function char_info($p_id) {
    if (!is_numeric($p_id) || !positive_int($p_id)) {
        return null;
    }

    $player = new Player($p_id); // Constructor uses DAO to get player object.
    $player_data = array();

    if ($player instanceof Player && $player->id()) {
        // Turn the player data vo into a simple array.
        $player_data = $player->data();
        $player_data['clan_id'] = ($player->getClan() ? $player->getClan()->getID() : null);
    }

    return $player_data;
}

function recordLevelUp($who) {
    $amount = 1;

    DatabaseConnection::getInstance();

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

// Takes in a character id and adds kills to that character.
function addKills($who, $amount) {
    return change_kills($who, (int)abs($amount));
}

function subtractKills($who, $amount) {
    return change_kills($who, -1*((int)abs($amount)));
}

// Change the kills amount of a char, and levels them up when necessary.
function change_kills($char_id, $amount) {
    update_levelling_log($who, $amount);

    $amount = (int)$amount;

    if (abs($amount) > 0) {
        // Ignore changes that amount to zero.
        if ($amount > 0) {
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

    return query_item(
        "SELECT kills FROM players WHERE player_id = :player_id",
        [
            ':player_id' => [$char_id, PDO::PARAM_INT]
        ]
    );
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

/**
 * query the recently active players
 */
function get_active_players($limit=5, $alive_only=true) {
	$where_cond = ($alive_only ? ' AND health > 0' : '');
	$sel = "SELECT uname, player_id FROM players WHERE active = 1 $where_cond ORDER BY last_started_attack DESC LIMIT :limit";
	$active_ninjas = query_array($sel, array(':limit'=>array($limit, PDO::PARAM_INT)));
	return $active_ninjas;
}

/**
 * Pull an array of different activity counts.
 */
function member_counts() {
	$counts = query_array("(SELECT count(session_id) FROM ppl_online WHERE member AND activity > (now() - CAST('30 minutes' AS interval)))
		UNION ALL (SELECT count(session_id) FROM ppl_online WHERE member)
		UNION ALL (select count(player_id) from players where active = 1)");
	$active_row = array_shift($counts);
	$online_row = array_shift($counts);
	$total_row = array_shift($counts);
	return array('active'=>reset($active_row), 'online'=>reset($online_row), 'total'=>end($total_row));
}
