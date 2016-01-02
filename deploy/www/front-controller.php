<?php
require_once(LIB_ROOT.'control/lib_clan.php');
require_once(CORE.'control/ClanController.php');
require_once(LIB_ROOT."control/lib_inventory.php");
require_once(LIB_ROOT."control/ShopController.php");

use app\Controller\ShopController;
use app\Controller\ClanController;

use Symfony\Component\HttpFoundation\Request;

$routes = [
	'clan' => [
		'new'     => 'create',
		'default' => 'listClans',
	],
	'shop' => [
		'default'  => 'index',
		'purchase' => 'buy',
	],
];

$request = Request::createFromGlobals();
$pathInfo = $request->getPathInfo();

$routeSegments = split('/', trim($pathInfo, '/'));

$controllerClass = "app\\Controller\\".ucfirst($routeSegments[0])."Controller";

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
	die();
}

if (method_exists($controller, $command)) {
	$action = $command;
} else if (isset($routes[$routeSegments[0]][$command])) {
	$action = $routes[$routeSegments[0]][$command];
} else {
	$action = $routes[$routeSegments[0]]['default'];
}

$response = $controller->$action();

display_page(
	$response['template'],
	$response['title'],
	$response['parts'],
	$response['options']
);
