<?php
namespace NinjaWars\core\control;

use Symfony\Component\HttpFoundation\RedirectResponse;
//use Symfony\Component\HttpFoundation\Request;
use \Constants;

/**
 * Log a player out via post, then redirect to logout landing page
 */
class LogoutController {
    const ALIVE = false;
    const PRIV  = false;

    public function index(){
    	logout_user();
    	return new RedirectResponse('/logout/loggedout');
    }

    public function loggedout(){
    	return ['template'=>'logout.tpl', 'title'=>'Logged Out', 'parts'=>null, 'options'=>null];
    }

}
