<?php

namespace NinjaWars\core\extensions;

class NWLogger
{
    public static function log($message, $level = 'info', $options = [])
    {
        $log_message = date('Y-m-d H:i:s') . ' [' . $level . '] ' . $message . "\n";
        error_log($log_message);
    }
}
