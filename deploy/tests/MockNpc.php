<?php
namespace NinjaWars\tests;


use NinjaWars\core\data\Character;

/**
 * Create a mock npc to manipulate
 */
class MockNpc implements Character{
    public $bounty_mod = 10;
    public $strength = 10;
    public $speed = 10;
    public $stamina = 10;
    public $max_damage = 10;
    public $health = 10;
    public $difficulty = 10;
    public $gold = 10;
    public $name = 'mock_name';

    public function gold(){
        return $this->gold;
    }

    public function bountyMod(){
        return $this->bounty_mod;
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

    public function health(){
        return $this->health;
    }

    public function difficulty(){
        return $this->difficulty;
    }

    public function __toString(){
        return $this->name;
    }
}
