<?php

// Test-suite bootstrap
require_once(realpath(__DIR__).'/../resources.php');
require_once(ROOT.'lib/base.inc.php');
require_once(ROOT.'tests/TestAccountCreateAndDestroy.php');
$_SERVER['REMOTE_ADDR'] = '127.0.0.1';