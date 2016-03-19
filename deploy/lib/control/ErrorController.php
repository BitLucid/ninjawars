<?php

namespace NinjaWars\core\control;

/**
 * Testing errors so that they return the right statuses if needed.
 */
class ErrorController{
    const PRIV          = false;
    const ALIVE         = false;
    public function index(){
        ini_set('display_errors', '0'); // Otherwise php returns 200 apparently
        trigger_error('Test error triggered', E_USER_ERROR);
    }
}