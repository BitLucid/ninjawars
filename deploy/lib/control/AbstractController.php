<?php
namespace NinjaWars\core\control;

use NinjaWars\core\data\Player;
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
     * @return string
     * @TODO this whole thing should be factored out.
     */
    public function validate() {
        $error  = null;
        $player = Player::find(SessionFactory::getSession()->get('player_id'));

        if ((!SessionFactory::getSession()->get('authenticated') || !$player) && static::PRIV) {
            $error = 'log_in';
        } elseif ($player && static::ALIVE) { // That page requires the player to be alive to view it
            if (!$player->health()) {
                $error = 'dead';
            } else if ($player->hasStatus(FROZEN)) {
                $error = 'frozen';
            }
        }

        return $error;
    }

    public function renderDefaultError($error) {
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
