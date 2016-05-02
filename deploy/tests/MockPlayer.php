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
    public function save(){
        return $this;
    }


    public function isAdmin(){
        return $this->isAdmin;
    }

    public function setAdmin($onoff){
        $this->isAdmin = $onoff;
    }

    public function set_gold($amount){
        $this->gold = $amount;
    }

    public function getStrength(){
        return $this->strength;
    }

    public function getSpeed(){
        return $this->speed;
    }

    public function getStamina(){
        return $this->stamina;
    }

    public function maxDamage(Character $char=null){
        return $this->max_damage;
    }

    public function damage(Character $char=null){
        return $this->damage;
    }

    public function difficulty(){
        return $this->difficulty;
    }

    public function __toString(){
        return $this->name;
    }
}
