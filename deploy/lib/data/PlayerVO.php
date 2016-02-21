<?php
namespace NinjaWars\core\data;

use NinjaWars\core\data\ValueObject;

/*
 * Value object for holding a player's data from the database.
 * Fields have to be added here and in the PlayerDAO.
 * Essentially this acts as the container for the model's data.
 * @var database_fields
 */
class PlayerVO extends ValueObject {
    public $player_id;
    public $uname;
    public $health;
    public $strength;
    public $speed;
    public $stamina;
    public $ki;
    public $karma;
    public $gold;
    public $messages;
    public $kills;
    public $turns;
    public $active;
    public $_class_id;
    public $level;
    public $status;
    public $days;
    public $bounty;
    public $created_date;
    public $last_started_attack;
    public $energy;
    public $avatar_type;
    public $verification_number;
    public $description;
    public $beliefs;
    public $goals;
    public $instincts;
    public $traits;

    // fields from class table
    public $identity;
    public $class_name;
    public $theme;
}
