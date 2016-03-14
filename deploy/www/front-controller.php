<?php
require_once(dirname(__DIR__.'..').'/lib/base.inc.php');

use NinjaWars\core\Router;
use Symfony\Component\HttpFoundation\Request;

try {
	// get the request information to parse the route
	Router::route(Request::createFromGlobals());
} catch (\RuntimeException $e) {
	include('404.php');
}
