<?php
namespace NinjaWars\core\control;

use Pimple\Container;
use NinjaWars\core\extensions\SessionFactory;
use NinjaWars\core\extensions\StreamedViewResponse;

abstract class AbstractController {
    /**
     * Check if a player can access any of this controllers methods
     *
     * This method is a holdover from old times when controllers were
     * actually directly servable procedural scripts (ah the old days!)
     * If a page required you to be alive or logged in, we checked if
     * you were (among a bunch of other things, usually dropping variables
     * in the global namespace) and if you were not, returned an error.
     * Now, controllers do this at a class level, which is mostly nonsense
     * and requires that the router directly serve an error page.
     *
     * @param Container
     * @return string
     * @TODO this whole thing should be factored out.
     */
    public function validate(Container $p_dependencies) {
        $error  = null;
        $player = $p_dependencies['current_player'];

        if (static::PRIV && (!$p_dependencies['session']->get('authenticated', false) || !$player)) {
            $error = 'log_in';
        } elseif (static::ALIVE && $player) { // The page requires the player to be alive to view it
            if ($player->health <= 0) {
                $error = 'dead';
            } else if ($player->hasStatus(FROZEN)) {
                $error = 'frozen';
            }
        }

        return $error;
    }

    public function renderDefaultError($error = "default") {
        return new StreamedViewResponse('There is an obstacle to your progress...', 'error.tpl', ['error' => $error], []);
    }

    /**
     * Get the current account_id from the session, if any
     * @return int|null
     */
    public function getAccountId(){
        return SessionFactory::getSession()->get('account_id');
    }
}
