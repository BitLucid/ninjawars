<?php
/*
 * Value object for holding a player's data from the database.
 * Fields have to be added here and in the PlayerDAO.
 * Essentially this acts as the container for the model's data.
 * @var database_fields
 */
class PlayerVO {
	public $player_id, $uname, $health, $strength, $gold,
	  	$messages, $kills, $turns, $active,
	  	$_class_id, $identity, $class_name, $theme,
	  	$level, $status, $days, $bounty, 
	  	$created_date, $last_started_attack, $energy, $avatar_type;
}
?>
