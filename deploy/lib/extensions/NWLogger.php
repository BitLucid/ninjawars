<?php

namespace NinjaWars\core\extensions;

class NWLogger
{
    public static function log(string $message, ?string $level = 'info', ?array $options = [])
    {
        $log_message = date('Y-m-d H:i:s') . ' [' . $level . '] ' . $message . "\n";
        error_log($log_message);
    }
}
