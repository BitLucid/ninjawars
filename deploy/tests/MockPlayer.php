<?php
namespace NinjaWars\tests;


use NinjaWars\core\data\Player;
use NinjaWars\core\data\Character;

/**
 * Create a mock player to manipulate
 */
class MockPlayer extends Player implements Character{
    public $strength = 10;
    public $speed = 10;
    public $stamina = 10;
    public $level = 10;
    public $max_damage = 10;
    public $health = 10;
    public $difficulty = 10;
    public $gold = 10;
    public $bounty = 10;
    public $turns = 10;
    public $ki = 10;
    public $name = 'mock_name';
    public $isAdmin = false;

    public function __get($name){
        return $this->$name;
    }

    /**
     * Mock the save, noop, just return the current object
     */
    public function save(): Player {
        return $this;
    }


    public function isAdmin(): bool{
        return $this->isAdmin;
    }

    public function setAdmin($onoff): void{
        $this->isAdmin = $onoff;
    }

    public function set_gold($amount): void{
        $this->gold = $amount;
    }

    public function getStrength(): int{
        return $this->strength;
    }

    public function getSpeed(): int{
        return $this->speed;
    }

    public function getStamina(): int{
        return $this->stamina;
    }

    public function maxDamage(Character $char=null): int{
        return $this->max_damage;
    }

    public function damage(Character $char=null): int{
        return $this->damage;
    }

    public function difficulty(): int{
        return $this->difficulty;
    }

    public function __toString(): string{
        return $this->name;
    }
}
