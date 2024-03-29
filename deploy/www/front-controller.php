<?php

require_once(dirname(__DIR__.'..').'/lib/base.inc.php');

use Pimple\Container;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use NinjaWars\core\RouteNotFoundException;
use NinjaWars\core\Router;
use NinjaWars\core\environment\RequestWrapper;
use NinjaWars\core\data\Player;
use NinjaWars\core\extensions\SessionFactory;

// setup our runtime environment
require_once(LIB_ROOT.'environment/bootstrap.php');

try {
    $container = new Container();

    $container['current_player'] = function ($c) {
        $player_id = SessionFactory::getSession()->get('player_id');
        return $player_id ? Player::find($player_id) : null;
    };

    $container['session'] = function ($c) {
        return SessionFactory::getSession();
    };

    // Update the activity of the page viewer in the database.
    RequestWrapper::init();

    // get the request information to parse the route
    $response = Router::route(Request::createFromGlobals(), $container);

    if ($response instanceof Response) {
        $response->send();
    } else {
        throw new \RuntimeException('Route returned something other than a Response');
    }
} catch (RouteNotFoundException $e) {
    Router::respond404();
}
