<?php
namespace NinjaWars\core\data;

/**
 * Npcs and Players should both implement this interface.
 */
interface Character {
    function strength();
    function speed();
    function stamina();
    function max_damage(Character $char=null);
    function damage(Character $char=null);
    function health();
}
