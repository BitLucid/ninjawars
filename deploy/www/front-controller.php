<?php
require_once(CORE.'Router.php');
require_once(LIB_ROOT."control/lib_inventory.php");
require_once(CORE.'control/ClanController.php');
require_once(CORE."control/ShopController.php");
require_once(CORE."control/CasinoController.php");
require_once(CORE.'control/WorkController.php');
require_once(CORE.'control/MessagesController.php');
require_once(CORE.'control/ShrineController.php');

use app\Core\Router;

use app\Controller\ShopController;
use app\Controller\ClanController;
use app\Controller\CasinoController;
use app\Controller\WorkController;
use app\Controller\MessagesController;
use app\Controller\ShrineController;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

/*
 * this is for custom routes. The default is to map /controller/action to
 * Controller->action() but if you wish to map it to Controller->action2() then
 * put an entry here. Adding a default entry helps for when the action is not
 * found or not provided, otherwise an error probably occurs.
 */
$routes = Router::$routes;

// get the request information to parse the route
$request = Request::createFromGlobals();
$pathInfo = $request->getPathInfo();

// split the requested path by slash
$routeSegments = explode('/', trim($pathInfo, '/'));

if ($routeSegments[0] === '') {
	include('index.php');
	exit();
} else if (stripos($routeSegments[0], '.php') === (strlen($routeSegments[0]) - 4)) {
	if (file_exists($routeSegments[0])) {
		include($routeSegments[0]);
		exit();
	}

	$routeSegments[0] = substr($routeSegments[0], 0, -4);
}

// dynamically define the controller classname
$controllerClass = "app\\Controller\\".ucfirst($routeSegments[0])."Controller";

if (!class_exists($controllerClass) || !isset($routes[$routeSegments[0]])) {
	include('404.php');
	exit();
}

// if there are 2 route segments use the second one, else use the command param
if (isset($routeSegments[1])) {
	$command = $routeSegments[1];
} else {
	$command = (string)in('command');
}

$controller = new $controllerClass();

$priv  = $controllerClass::PRIV;
$alive = $controllerClass::ALIVE;

if ($error = init($priv, $alive)) {
	display_error($error);
} else {
	/*
	 * if the action requested is a named method on the controller class, call
	 * it. Otherwise, look up the action in the routes array. If it's not there
	 * try the default route. If none specified, throw.
	 */
	if (method_exists($controller, $command)) {
		$action = $command;
	} else if (isset($routes[$routeSegments[0]][$command])) {
		$action = $routes[$routeSegments[0]][$command];
	} else if (isset($routes[$routeSegments[0]]['default'])) {
		$action = $routes[$routeSegments[0]]['default'];
	} else {
		throw new RuntimeException('Requested route not found.');
	}

	$response = $controller->$action();

	if($response instanceof RedirectResponse){
		$response->send();
	} else {
		display_page(
			$response['template'],
			$response['title'],
			$response['parts'],
			$response['options']
		);
	}
}
