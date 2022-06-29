<?php

namespace NinjaWars\tests;

use NinjaWars\core\data\Deity;

class MockDeity
{
    public const VICIOUS_KILLER_STAT = 4; // the ID of the vicious killer stat
    public const MIDNIGHT_HEAL_SKILL = 5; // the ID of the midnight heal skill
    public const LEVEL_REGEN_INCREASE = false;
    public const LEVEL_REVIVE_INCREASE = false;
    public const DEFAULT_REGEN = 3;

    /**
     * Use dynamic call to return 1 ints generally
     */
    public function __call($name, $arguments)
    {
        return 1;
    }

    /**
     * Use static call replacement to return 1 ints generally
     */
    public static function __callStatic($method, $args)
    {
        return 1;
    }
}
