<?php
use NinjaWars\core\data\DatabaseConnection;
use NinjaWars\core\data\ClanFactory;
use NinjaWars\core\data\AccountFactory;
use NinjaWars\core\data\GameLog;

require_once(LIB_ROOT."control/lib_status.php");
require_once(LIB_ROOT."control/lib_accounts.php");

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
