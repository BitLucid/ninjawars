<?php
require_once(dirname(__DIR__.'..').'/lib/base.inc.php');

use NinjaWars\core\Router;
use Symfony\Component\HttpFoundation\Request;
use NinjaWars\core\RouteNotFoundException;

if (defined('TRAP_ERRORS') && TRAP_ERRORS) {
    set_exception_handler(['NWError', 'exceptionHandler']);
    set_error_handler(['NWError', 'errorHandler'], E_USER_ERROR);
}

try {
    // get the request information to parse the route
    Router::route(Request::createFromGlobals());
} catch (RouteNotFoundException $e) {
    Router::respond404();
}
