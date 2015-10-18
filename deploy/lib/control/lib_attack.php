<?php
/**
 * Attack function library.
 * @package combat
 * @subpackage lib_attack
**/

function update_last_attack_time($player_id) {
	DatabaseConnection::getInstance();

	$update_last_attacked = "UPDATE players SET last_started_attack = now() WHERE player_id = :user";

	$statement = DatabaseConnection::$pdo->prepare($update_last_attacked);
	$statement->bindValue(':user', intval($player_id));

	$statement->execute(); // updates the timestamp of the last_attacked column to slow excessive attacks.
}

function killpointsFromDueling() { //  *** Multiple Killpoints from Dueling ***
	global $target_level,$attacker_level,$starting_target_health,$killpoints,$duel;

	$levelDifference = ($target_level-$attacker_level);

	if ($levelDifference > 10) {
		$levelDifferenceMultiplier = 5;
	} else if ($levelDifference > 0) {
		$levelDifferenceMultiplier = ceil($levelDifference/2);  //killpoint return of half the level difference.
	} else {
		$levelDifferenceMultiplier = 0;
	}

	$killpoints = 1+$levelDifferenceMultiplier;
}
