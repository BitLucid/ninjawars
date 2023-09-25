<?php

// Test-suite bootstrap
require_once(realpath(__DIR__).'/../resources.php');
require_once(CORE.'base.inc.php');
require_once(ROOT.'tests/TestAccountCreateAndDestroy.php');
require_once(ROOT.'tests/NWTest.php');
//use NinjaWars\tests\TestAccountCreateAndDestroy;
$_SERVER['REMOTE_ADDR'] = '127.0.0.1';

class NullTestTransport
{
    public function putEvents($params)
    {
        return [
            'FailedEntryCount' => 0,
            'Entries' => [
                [
                    'EventId' => '1234',
                    'ErrorCode' => null,
                    'ErrorMessage' => null,
                ]
            ]
        ];
    }
}

Nmail::$transport = new NullTestTransport();
