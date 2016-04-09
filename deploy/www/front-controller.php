<?php
require_once(dirname(__DIR__.'..').'/lib/base.inc.php');

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use NinjaWars\core\RouteNotFoundException;
use NinjaWars\core\Router;

// setup our runtime environment
require_once(LIB_ROOT.'environment/bootstrap.php');

try {
    update_activity_info(); // Updates the activity of the page viewer in the database.

    // get the request information to parse the route
    $response = Router::route(Request::createFromGlobals());

    if ($response instanceof Response) {
        $response->send();
    } else if (is_array($response)) {
        Router::render($response);
    } else {
        echo $response;
    }
} catch (RouteNotFoundException $e) {
    Router::respond404();
}
