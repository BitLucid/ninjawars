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

    /*
     * Adding a default entry helps for when the action is not found or not
     * provided, otherwise a 404 will occur.
     */
    public static $routes = [
		'login' => [
			'login_request' => 'requestLogin',
		],
        'clan' => [
            'new'     => 'create',
            'default' => 'listClans',
        ],
        'shop' => [
            'purchase' => 'buy',
        ],
        'work' => [
            'request_work' => 'requestWork',
        ],
        'shrine' => [
            'heal_and_resurrect' => 'healAndResurrect',
        ],
		'doshin' => [
			'Bribe'        => 'bribe',
			'Offer Bounty' => 'offerBounty',
		],
		'stats' => [
			'change_details' => 'changeDetails',
			'update_profile' => 'updateProfile',
		],
        'messages' => [
            'delete_clan'     => 'deleteClan',
            'delete_messages' => 'deletePersonal',
            'send_clan'       => 'sendClan',
            'send_personal'   => 'sendPersonal',
            'clan'            => 'viewClan',
            'default'         => 'viewPersonal',
        ],
		'account' => [
			'show_change_email_form'    => 'showChangeEmailForm',
			'change_email'              => 'changeEmail',
			'show_change_password_form' => 'showChangePasswordForm',
			'change_password'           => 'changePassword',
			'show_confirm_delete_form'  => 'deleteAccountConfirmation',
			'delete_account'            => 'deleteAccount',
		]
    ];

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
    public static function parseRoute($p_request) {
        $pathInfo = $p_request->getPathInfo();

        // split the requested path by slash
        $routeSegments = explode('/', trim($pathInfo, '/'));

        // if there are 2 route segments use the second one
        if (!isset($routeSegments[1])) {
            $routeSegments[1] = (string)in(self::COMMAND_PARAM);
        }

        if (empty($routeSegments[0])) {
            $routeSegments[0] = self::DEFAULT_ROUTE;
        }

		if (empty($routeSegments[1])) {
			if (isset(self::$routes[$routeSegments[0]][self::DEFAULT_COMMAND])) {
				$routeSegments[1] = self::$routes[$routeSegments[0]][self::DEFAULT_COMMAND];
			} else {
				$routeSegments[1] = self::DEFAULT_ACTION;
			}
		}

        return $routeSegments;
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
    public static function sanitizeMainRoute($p_main) {
        if (stripos($p_main, '.php') === (strlen($p_main) - 4)) {
            $p_main = substr($p_main, 0, -4);
        }

		if ($p_main === 'doshin_office') {
			$p_main = 'doshin';
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
        $mainRoute = self::sanitizeMainRoute($p_main);

        // dynamically define the controller classname
        $controllerClass = self::buildClassName($mainRoute);

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
        } else if (isset(self::$routes[$mainRoute][$p_command])) {
            $action = self::$routes[$mainRoute][$p_command];
        } else {
            throw new \RuntimeException();
        }

        $priv  = $controllerClass::PRIV;
        $alive = $controllerClass::ALIVE;

        if ($error = init($priv, $alive)) {
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
        $routeSegments = self::parseRoute($p_request);

        $mainRoute = array_shift($routeSegments);
        $command = array_shift($routeSegments);

        if (
            (stripos($mainRoute, '.php') === (strlen($mainRoute) - 4)) &&
            (file_exists($mainRoute) && realpath($mainRoute) === getcwd().'/'.$mainRoute)
        ) {
            include($mainRoute);
        } else {
            $response = self::execute($mainRoute, $command);

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
