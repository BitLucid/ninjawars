<?php
if (DEBUG && DEBUG_ALL_ERRORS) {
    error_reporting(E_ALL);	// *** Completely everything ***
}

if (!DEBUG) {
    assert_options(ASSERT_ACTIVE, 0); // *** Sets assert off on live.
} else {
    assert_options(ASSERT_ACTIVE, 1);
}

$dbg = in('debug');

if ($dbg === 'on') {
    $_COOKIE['debug'] == true;
} elseif ($dbg === 'off') {
    $_COOKIE['debug'] == false;

}
if (defined('TRAP_ERRORS') && TRAP_ERRORS) {
    set_exception_handler(['NWError', 'exceptionHandler']);
    set_error_handler(['NWError', 'errorHandler'], E_USER_ERROR);
}
