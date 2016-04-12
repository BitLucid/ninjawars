<?php
namespace NinjaWars\core;

use NinjaWars\core\RouteNotFoundException;
use NinjaWars\core\extensions\NWTemplate;
use NinjaWars\core\data\Player;
use NinjaWars\core\extensions\SessionFactory;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

/**
 * Router/front-controller for NinjaWars
 *
 * By default, routes of the form /controller/action are mapped to
 * Controller->action(). Overrides are defined here in the $routes member.
 */
class Router {
    const CONTROLLER_NS   = 'NinjaWars\core\control'; /// Namespace for controllers
    const DEFAULT_ACTION  = 'index';
    const DEFAULT_COMMAND = 'default';
    const DEFAULT_ROUTE   = 'homepage';
    const COMMAND_PARAM   = 'command';

    /**
     * Set during bootstapping by the file routes.php. Routes are defined as a
     * 3-level nested-array with level-one keys being the controller name,
     * level-two keys being metadata, and level-three keys being action names.
     * Action names are mapped to public methods on the controller class.
     */
    public static $routes;

    /**
     * Set during bootstrapping by the file routes.php. Mappings between the
     * controller portion of a route and a real controller name can be used to
     * make nicer routes or to support historical URLs.
     */
    public static $controllerAliases;

    /**
     * Given a request, includes a file or runs a controller action
     *
     * @param Request The request from the web server
     * @return none
     *
     * @note
     * This method generates output
     *
     * @todo remove the second isServableFile block when an index controller exists
     */
    public static function route($p_request) {
        // split the requested path by slash
        $routeSegments = explode('/', trim($p_request->getPathInfo(), '/'));

        $mainRoute = current($routeSegments); // get 1st part of route

        if (self::isServableFile($mainRoute)) { // serve existing files
            include($mainRoute);
        } else { // attempt to serve controller actions
            $routeSegments = self::parseRoute($p_request);
            $mainRoute = array_shift($routeSegments);

            /** Because we don't have an index controller, we serve index.php
             * as a file. Because index.php is the default route, we need this
             * repetition of code because parseRoute translates / to index.php
             */
            if (self::isServableFile($mainRoute)) {
                include($mainRoute);
            } else {
                if (isset(self::$routes[$mainRoute]) && self::$routes[$mainRoute]['type'] === 'simple') {
                    $response = self::serveSimpleRoute($mainRoute);
                } else {
                    $command   = array_shift($routeSegments);
                    $response  = self::execute($mainRoute, $command);
                }

                return $response;
            }
        }
    }

    /**
     * Breaks the request URI into an array of route segments
     *
     * @param Request The request object being routed
     * @return array Each segment of the route
     *
     * @note
     * If there is no path in the request, this will return an array with the
     * default route for the application
     *
     * @note
     * If there is only 1 route segment and the special input parameter
     * "command" is set in the request, the value of "command" will be used
     * as the second element of the return value. If there is no command
     * parameter, the default action for the application will be used
     *
     * @todo remove the call to in() and replace with something from request
     * @todo stop supporting ?command=action
     */
    public static function parseRoute($p_request) {
        // split the requested path by slash
        $routeSegments = explode('/', trim($p_request->getPathInfo(), '/'));

        if (empty($routeSegments[0])) { // Route is just forward-slash
            $mainRoute = self::DEFAULT_ROUTE;
        } else { // determine the canonical route being requested
            $mainRoute = self::translateRoute(
                self::sanitizeRoute($routeSegments[0])
            );
        }

        // if there are 2 route segments use the second one as the command
        if (isset($routeSegments[1]) && !empty($routeSegments[1])) {
            $command = $routeSegments[1];
        } else { // without a 2nd route segment, look for command in the input
            $command = (string)in(self::COMMAND_PARAM);
        }

        if (empty($command)) {
            if (isset(self::$routes[$mainRoute]['actions'][self::DEFAULT_COMMAND])) {
                $command = self::$routes[$mainRoute]['actions'][self::DEFAULT_COMMAND];
            } else {
                $command = self::DEFAULT_ACTION;
            }
        }

        return [$mainRoute, $command];
    }

    /**
     * Takes a string and returns a fully qualified controller classname
     *
     * @param string $p_main The token to turn into a classname
     * @return string A fully qualified controller classname
     */
    public static function buildClassName($p_main) {
        return self::CONTROLLER_NS.'\\'.ucfirst($p_main)."Controller";
    }

    /**
     * Removes a trailing ".php" from the inputted string
     *
     * @param string $p_main
     * @return string
     */
    public static function sanitizeRoute($p_main) {
        if (stripos($p_main, '.php') === (strlen($p_main) - 4)) {
            $p_main = substr($p_main, 0, -4);
        }

        return $p_main;
    }

    /**
     * Dereferences the inputted string if an alias exists for it
     *
     * @param string $p_main
     * @return string
     */
    public static function translateRoute($p_main) {
        return (isset(self::$controllerAliases[$p_main]) ?
            self::$controllerAliases[$p_main] : $p_main);
    }

    /**
     * Runs the requested route and returns the ViewSpec to render
     *
     * Given a main route, this function instantiates the necessary controller
     * and calls the necessary method on it, returning the result. It also
     * handles the error condition when the requested route is not allowed to
     * be executed by the requestor.
     *
     * @param string $p_main The main route segment to execute
     * @param string $p_command The command to execute on the main route
     * @return array The ViewSpec to render
     * @throws RouteNotFoundException No controller could be found for $p_main
     * @throws RouteNotFoundException No public method could be found for $p_command
     * @todo Throw a specific exception when class not found
     * @todo Throw a specific exception when the command requested is not found
     * @todo Abstract out the rendering of the error screen
     */
    public static function execute($p_main, $p_command) {
        // dynamically define the controller classname
        $controllerClass = self::buildClassName($p_main);

        if (!class_exists($controllerClass)) { // ensure class requested exists
            throw new RouteNotFoundException();
        }

        $controller = new $controllerClass();

        /* if the action requested is a named method on the controller class,
         * call it. Otherwise, look up the action in the routes map. If it's
         * not there try the default route. If none specified, throw.
         */
        if (is_callable([$controller, $p_command])) {
            $action = $p_command;
        } else if (isset(self::$routes[$p_main]['actions'][$p_command])) {
            $action = self::$routes[$p_main]['actions'][$p_command];

            // If the action in the routes map still doesn't exist, throw
            if (!is_callable([$controller, $action])) {
                throw new RouteNotFoundException();
            }
        } else {
            throw new RouteNotFoundException();
        }

        if ($error = $controller->validate()) {
            return $controller->renderDefaultError($error);
        } else {
            return $controller->$action();
        }
    }

    /**
     * Determines if the route requested is actually a file in the web root
     *
     * @param string $p_route The route requested by the user
     * @return boolean
     */
    public static function isServableFile($p_route) {
        return (is_file($p_route) && realpath($p_route) === getcwd().'/'.$p_route);
    }

    /**
     * Generates a viewspec for a route served by a view without a controller
     *
     * @param string $p_mainRoute The 1st-level route requested by the user
     * @return Viewspec The data needed to render the view on from the route
     */
    public static function serveSimpleRoute($p_mainRoute) {
        return [
            'template' => "$p_mainRoute.tpl",
            'title'    => self::$routes[$p_mainRoute]['title'],
            'parts'    => [],
            'options'  => false,
        ];
    }

    /**
     * Return 404 and 404 headers
     */
    public static function respond404() {
        header( $_SERVER['SERVER_PROTOCOL']." 404 Not Found", true, 404 ); //.replace = true
        $view = new NWTemplate();
        $view->display('404.tpl');
    }

    /**
     * Renders the view and sends it to the client
     *
     * @param Array $p_viewSpec The data needed to render a view
     * @return void
     * @note
     * This method generates output
     */
    public static function render($p_viewSpec) {
        if (isset($p_viewSpec['raw'])) {
            $response = new Response($p_viewSpec['raw']);
        } else {
            $response = new StreamedResponse();

            $response->setCallback(function() use ($p_viewSpec) {
                $view = new NWTemplate();
                $view->displayPage(
                    $p_viewSpec['template'],
                    $p_viewSpec['title'],
                    $p_viewSpec['parts'],
                    $p_viewSpec['options']
                );
            });
        }

        if (isset($p_viewSpec['headers'])) {
            $response->headers = new ResponseHeaderBag($p_viewSpec['headers']);
        }

        $response->send();
    }
}
