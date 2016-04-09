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
    update_activity_info(); // Updates the activity of the page viewer in the database.

    // get the request information to parse the route
    $response = Router::route(Request::createFromGlobals());

    if ($response instanceof RedirectResponse) {
        $response->send();
    } else {
        Router::render($response);
    }
} catch (RouteNotFoundException $e) {
    Router::respond404();
}
