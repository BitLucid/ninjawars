<?php
require_once(dirname(__DIR__.'..').'/lib/base.inc.php');

use NinjaWars\core\Router;
use Symfony\Component\HttpFoundation\Request;
use NinjaWars\core\RouteNotFoundException;

try {
	// get the request information to parse the route
	Router::route(Request::createFromGlobals());
} catch (\RouteNotFoundException $e) {
	include(WEB_ROOT.'404.php');
}
