<?php

use Symfony\Component\Mailer\Transport;

// Test-suite bootstrap
require_once(realpath(__DIR__).'/../resources.php');
require_once(CORE.'base.inc.php');
require_once(ROOT.'tests/TestAccountCreateAndDestroy.php');
require_once(ROOT.'tests/NWTest.php');
//use NinjaWars\tests\TestAccountCreateAndDestroy;
$_SERVER['REMOTE_ADDR'] = '127.0.0.1';

Nmail::$transport = Transport::fromDsn('null://null');
