<?php
namespace NinjaWars\core;

use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Router/front-controller for NinjaWars
 *
 * By default, routes of the form /controller/action are mapped to
 * Controller->action(). Overrides are defined here in the $routes member.
 */
class Router {
    const CONTROLLER_NS   = 'NinjaWars\core\control';
    const DEFAULT_ACTION  = 'index';
    const DEFAULT_COMMAND = 'default';
    const DEFAULT_ROUTE   = 'index.php';
    const COMMAND_PARAM   = 'command';

    public static $routes;
    public static $controllerAliases;

    /**
     * Breaks the request URI into an array of route segments
     *
     * @param Request The request object being routed
     * @return array Each segment of the route
     *
     * @note
     * If there is only 1 route segment and the special input parameter
     * "command" is set in the request, the value of "command" will be returned
     * in as the second element of the returned array
     *
     * @todo remove the call to in() and replace with something from request
     * @todo stop supporting ?command=action
     */
    public static function parseRoute($p_routeSegments, $p_request) {
        $p_request = $p_request;

        if (empty($p_routeSegments[0])) {
            $mainRoute = self::DEFAULT_ROUTE;
        } else {
            $mainRoute = self::translateRoute(
                self::sanitizeRoute($p_routeSegments[0])
            );
        }

        // if there are 2 route segments use the second one
        if (isset($p_routeSegments[1]) && !empty($p_routeSegments[1])) {
            $command = $p_routeSegments[1];
        } else {
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
        if (isset(self::$controllerAliases[$p_main])) {
            $p_main = self::$controllerAliases[$p_main];
        }

        return $p_main;
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
     * @throws RuntimeException No controller could be found for $p_main
     * @throws RuntimeException No public method could be found for $p_command
     */
    public static function execute($p_main, $p_command) {
        // dynamically define the controller classname
        $controllerClass = self::buildClassName($p_main);

        if (!class_exists($controllerClass)) {
            throw new \RuntimeException();
        }

        $controller = new $controllerClass();

        /*
         * if the action requested is a named method on the controller class, call
         * it. Otherwise, look up the action in the routes array. If it's not there
         * try the default route. If none specified, throw.
         */
        if (method_exists($controller, $p_command)) {
            $action = $p_command;
        } else if (isset(self::$routes[$p_main]['actions'][$p_command])) {
            $action = self::$routes[$p_main]['actions'][$p_command];

            if (!method_exists($controller, $action)) {
                throw new \RuntimeException();
            }
        } else {
            throw new \RuntimeException();
        }

        if ($error = init($controllerClass::PRIV, $controllerClass::ALIVE)) {
            return [
                'template' => 'error.tpl',
                'title'    => 'There is an obstacle to your progress...',
                'parts'    => ['error' => $error],
                'options'  => [],
            ];
        } else {
            return $controller->$action();
        }
    }

    public static function isServableFile($p_route) {
        return (file_exists($p_route) && realpath($p_route) === getcwd().'/'.$p_route);
    }

    /**
     * The primary method used by the front-controller script to run the app
     *
     * @param Request The request from the web server
     * @return none
     *
     * @note
     * This method generates output
     */
    public static function route($p_request) {
        $pathInfo = $p_request->getPathInfo();

        // split the requested path by slash
        $routeSegments = explode('/', trim($pathInfo, '/'));

        $mainRoute = current($routeSegments);

        if (self::isServableFile($mainRoute)) {
            include($mainRoute);
        } else {
            $routeSegments = self::parseRoute($routeSegments, $p_request);
            $mainRoute = array_shift($routeSegments);

            if (self::isServableFile($mainRoute)) {
                include($mainRoute);
            } else {
                if (isset(self::$routes[$mainRoute]) && self::$routes[$mainRoute]['type'] === 'simple') {
                    $response = [
                        'template' => "$mainRoute.tpl",
                        'title'    => self::$routes[$mainRoute]['title'],
                        'parts'    => [],
                        'options'  => false,
                    ];
                } else {
                    $command   = array_shift($routeSegments);
                    $response  = self::execute($mainRoute, $command);
                }

                if ($response instanceof RedirectResponse) {
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
        }
    }
}
