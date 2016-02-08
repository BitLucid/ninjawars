<?php
namespace NinjaWars\core\data;

use NinjaWars\core\data\ValueObject;

/*
 * Value object for holding a player's data from the database.
 * Fields have to be added here and in the PlayerDAO.
 * Essentially this acts as the container for the model's data.
 * @var database_fields
 */
class PlayerVO extends ValueObject{
	public $player_id, $identity, $uname, $health, $strength, $speed, $stamina,
		$ki, $karma, $gold, $messages, $kills, $turns, $active,
	  	$_class_id, $class_name, $theme,
	  	$level, $status, $days, $bounty, 
	  	$created_date, $last_started_attack, $energy, $avatar_type, $verification_number, 
	  	$description, $beliefs, $goals, $instincts, $traits;
}
