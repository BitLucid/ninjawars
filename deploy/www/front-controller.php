<?php
require_once(CORE.'Router.php');

use app\Core\Router;

use Symfony\Component\HttpFoundation\Request;

try {
	// get the request information to parse the route
	Router::route(Request::createFromGlobals());
} catch (RuntimeException $e) {
	include('404.php');
}
