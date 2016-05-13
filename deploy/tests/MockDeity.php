<?php
namespace NinjaWars\tests;

class MockDeity{
    const VICIOUS_KILLER_STAT = 4; // the ID of the vicious killer stat
    const MIDNIGHT_HEAL_SKILL = 5; // the ID of the midnight heal skill
    const LEVEL_REGEN_INCREASE = false;
    const LEVEL_REVIVE_INCREASE = false;
    const DEFAULT_REGEN = 3;

    /** 
     * Use dynamic call to return 1 ints generally
     */
    function __call($name, $arguments){
        return 1;
    }

    /** 
     * Use static call replacement to return 1 ints generally
     */
    public static function __callStatic($method, $args){
        return 1;
    }
}