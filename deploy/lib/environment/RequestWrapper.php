<?php
namespace NinjaWars\core\environment;

use Symfony\Component\HttpFoundation\Request;
use \Constants;

/**
 * Creates an API for using a repeatable request and other globals
 */
class RequestWrapper{
    public static $request = null;

    /**
     */
    public static function init() {
        if (!static::$request) {
            Request::setTrustedProxies(Constants::$trusted_proxies);
            // Create request object from global page request otherwise.
            static::$request = Request::createFromGlobals();
        }
        // Otherwise, the request will be pre-injected.
    }

    /**
     * Inject a request object if unavailable, e.g. on cli or
     */
    public static function inject(Request $request) {
        static::$request = $request;
    }

    /**
     * Nullify the static request, generally for unit testing.
     */
    public static function destroy() {
        static::$request = null;
    }

    /**
     * Get url parameter by key
     */
    public static function get($val, $default=null) {
        static::init();
        return static::$request->query->get($val, $default);
    }

    /**
     * Get the all the path info after the / of the domain
    **/
    public static function getPathInfo(){
        static::init();
        return static::$request->getPathInfo();
    }

    /**
     * Post parameter by key
     */
    public static function getPost($val, $default=null) {
        static::init();
        return static::$request->request->get($val, $default);
    }

    /**
     * Equivalent to $_REQUEST
     */
    public static function getPostOrGet($val) {
        return (static::getPost($val) ? static::getPost($val) : static::get($val));
    }
}
