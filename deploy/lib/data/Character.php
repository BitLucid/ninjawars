<?php
namespace NinjaWars\core\data;

/**
 * Npcs and Players should both implement this interface.
 */
interface Character {

    /**
     * @return int
     */
    function getStrength();

    /**
     * @return int
     */
    function getSpeed();

    /**
     * @return int
     */
    function getStamina();

    /**
     * @return int
     */
    function maxDamage(Character $char=null);

    /**
     * @return int
     */
    function damage(Character $char=null);

    /**
     * @return int
     */
    function health();

    /**
     * @return int
     */
    function difficulty();
}
