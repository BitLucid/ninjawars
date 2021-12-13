<?php

/**
 * Clear out the session context, then go to a static page
 */
namespace NinjaWars\core\control;

use NinjaWars\core\control\AbstractController;
use NinjaWars\core\extensions\SessionFactory;
use NinjaWars\core\extensions\StreamedViewResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use \Constants;

/**
 * Log a player out via post, then redirect to logout landing page
 */
class LogoutController extends AbstractController {
    const ALIVE = false;
    const PRIV  = false;

    public function index(): RedirectResponse
    {
        $session = SessionFactory::getSession();
        $session->clear();
        $session->invalidate();
    	return new RedirectResponse('/logout/loggedout');
    }

    public function loggedout(): StreamedViewResponse
    {
        return new StreamedViewResponse('Logged Out', 'logout.tpl');
    }
}
