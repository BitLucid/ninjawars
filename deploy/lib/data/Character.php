<?php

namespace NinjaWars\core\data;

/**
 * Npcs and Players should both implement this interface.
 */
interface Character {
    /**
     * @return int
     */
    public function getStrength();

    /**
     * @return int
     */
    public function getSpeed();

    /**
     * @return int
     */
    public function getStamina();

    /**
     * @return int
     */
    public function maxDamage(Character $char=null);

    /**
     * @return int
     */
    public function damage(Character $char=null);

    /**
     * @return int
     */
    public function getHealth();

    /**
     * @return int
     */
    public function difficulty();
}
