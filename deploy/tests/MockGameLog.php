<?php
namespace NinjaWars\tests;

use NinjaWars\core\data\GameLog;

class MockGameLog extends GameLog{
    public function log($log_message, $priority=0){
        // Noop for mock
        return true;
    }
}