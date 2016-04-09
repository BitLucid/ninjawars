<?php
namespace NinjaWars\core\control;

use NinjaWars\core\control\AbstractController;
use NinjaWars\core\extensions\SessionFactory;
use Symfony\Component\HttpFoundation\RedirectResponse;
use \Constants;

/**
 * Log a player out via post, then redirect to logout landing page
 */
class LogoutController extends AbstractController {
    const ALIVE = false;
    const PRIV  = false;

    public function index() {
        $session = SessionFactory::getSession();
        $session->clear();
        $session->invalidate();

    	return new RedirectResponse('/logout/loggedout');
    }

    public function loggedout() {
    	return ['template'=>'logout.tpl', 'title'=>'Logged Out', 'parts'=>null, 'options'=>null];
    }

}
