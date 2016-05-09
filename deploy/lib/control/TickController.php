<?php
namespace NinjaWars\core\control;

use NinjaWars\core\data\Deity;
use \InvalidArgumentException;

/**
 * Control the game ticks.
 */
class TickController{

        /**
     * @param int $minutes Minute interval of tick.
     */
    public static function run($static_class, $minutes){
        switch($minutes){
            case 1:
                $static_class::atomic();
            break;
            case 5:
                $static_class::tiny();
            break;
            case 30:
                $static_class::minor();
            break;
            case 60:
                $static_class::major();
            break;
            case 1440:
                $static_class::daily();
            break;
            default:
                throw new InvalidArgumentException('Unusable time interval');
            break;
        }
    }
}