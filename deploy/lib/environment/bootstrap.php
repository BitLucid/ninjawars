<?php
use NinjaWars\core\environment\RequestWrapper;

if (DEBUG && DEBUG_ALL_ERRORS) {
    error_reporting(E_ALL);	// *** Completely everything ***
}

if (!DEBUG) {
    assert_options(ASSERT_ACTIVE, 0); // *** Sets assert off on live.
} else {
    assert_options(ASSERT_ACTIVE, 1);
}

$dbg = RequestWrapper::getPostOrGet('debug');

if ($dbg === 'on') {
    $_COOKIE['debug'] == true;
} elseif ($dbg === 'off') {
    $_COOKIE['debug'] == false;

}

if (defined('TRAP_ERRORS') && TRAP_ERRORS) {
    set_exception_handler(['NWError', 'exceptionHandler']);
    set_error_handler(['NWError', 'errorHandler'], E_USER_ERROR);
}

if (defined('PROFILE') && PROFILE) {
    $mtime = microtime();
    $mtime = explode(" ",$mtime);
    $mtime = $mtime[1] + $mtime[0];
    $totaltime = ($mtime - $__starttime);
    $totalmemory = memory_get_peak_usage(true);
    $unit=array('b','kb','mb','gb','tb','pb');
    $totalmemory = @round($totalmemory/pow(1024,($i=floor(log($totalmemory,1024)))),2).' '.$unit[$i];
    error_log('PROFILE - Script: '.$_SERVER["SCRIPT_NAME"]." - Time: $totaltime - Mem: $totalmemory");
}
