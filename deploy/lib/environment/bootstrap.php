<?php
use NinjaWars\core\environment\RequestWrapper;

if (DEBUG) {
    error_reporting(E_ALL);	// *** Completely everything ***
}

if (defined('TRAP_ERRORS') && TRAP_ERRORS) {
    set_exception_handler(['NWError', 'exceptionHandler']);
    set_error_handler(['NWError', 'errorHandler'], E_USER_ERROR);
}
