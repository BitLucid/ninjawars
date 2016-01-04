<?php
namespace NinjaWars\core\control;

/**
 * Handles all user requests for the in-game Doshin Office
 */
class DoshinController { //extends controller
    const ALIVE          = true;
    const PRIV           = false;
    const MAX_BOUNTY     = 5000;
    const MIN_BRIBE      = 0;
    const DOSHIN_CUT     = .8;
    const SAFE_WEALTH    = 1000;
    const RICH_REDUCTION = .7;

    protected $sessionData = [];

    /**
     * Gathers data from session and makes it available to internal methods
     *
     * @return DoshinController
     */
    public function __construct() {
        $this->sessionData = [
            'username' => self_name(),
            'char_id'  => self_char_id(),
        ];
    }

    /**
     * Displays the initial Doshin Office view
     *
     * @param target String (Optional) Pre-load the bounty form with the specified target
     * @return Array
     */
    public function index() {
        $target = in('target');

        return $this->render(
            [
                'quickstat' => true,
                'location'  => 0,
                'error'     => 0,
                'command'   => 'index',
                'target'    => $target,
            ]
        );
    }

    /**
     * Command for the current user to offer their money as bounty on another player
     *
     * @param target String The username of the player to offer a bounty on
     * @param amount int The amount of gold to spend on offering the bounty
     * @return Array
     *
     * @TODO simplify the conditional branching
     */
    public function offerBounty() {
        $target    = in('target');
        $target_id = get_char_id($target); // Will be the enemy to put the bounty on.
        $amount    = intval(in('amount'));
        $amount_in = $amount;
        $error     = 0;
        $quickstat = false;
        $success   = false;

        if (!$target_id) { // Test that target exists
            $error = 1;
        } else {
            $target_bounty = getBounty($target_id);

            // Cap possible bounty amount
            if (($target_bounty + $amount) > self::MAX_BOUNTY) {
                $amount = (self::MAX_BOUNTY - $target_bounty);
            }

            if (get_gold($this->sessionData['char_id']) < $amount) {
                $error = 2;
            }

            if ($amount <= 0) {
                $error = 3;
            }

            if ($target_bounty >= 5000) {
                $error = 4;
            }
        }

        if ($error === 0) {
            addBounty($target_id, $amount); // Add the bounty to the person being bountied upon.  How the hell did this break?

            subtract_gold($this->sessionData['char_id'], $amount);
            send_event($this->sessionData['char_id'], $target_id, $this->sessionData['username']." has offered $amount gold in reward for your head!");

            $success = true;
            $quickstat = 'player';
        }

        return $this->render(
            [
                'error'     => $error,
                'success'   => $success,
                'quickstat' => $quickstat,
                'amount_in' => $amount_in,
                'amount'    => $amount,
                'command'   => 'offer',
                'location'  => 0,
                'target'    => $target,
            ]
        );
    }

    /**
     * Command for a user to reduce their bounty by paying their own gold
     *
     * @param bribe int The amount to spend on reducing bounty
     * @return Array
     */
    public function bribe() {
        $bribe = intval(in('bribe'));
        $error = 0;
        $quickstat = false;

        if ($bribe <= get_gold($this->sessionData['char_id']) && $bribe > 0) {
            subtract_gold($this->sessionData['char_id'], $bribe);
            subtractBounty($this->sessionData['char_id'], ($bribe/2));
            $location = 1;
            $quickstat = 'player';
        } else if ($bribe < self::MIN_BRIBE) {
            $this->doshinAttack($this->SessionData['char_id']);
            $location = 2;
            $quickstat = 'player';
        } else {
            $location = 0;
            $error = 5;
        }

        return $this->render(
            [
                'error'     => $error,
                'quickstat' => $quickstat,
                'location'  => $location,
                'command'   => 'bribe',
            ]
        );
    }

    /**
     * Was a bug, now the doshin beats you up! Yay!
     *
     * @note
     * If player has enough gold, their bounty will be mostly removed
     * by this event.
     */
    private function doshinAttack($p_characterId) {
        if (get_gold($p_characterId) > self::SAFE_WEALTH) {
            $bountyReduction = (getBounty($p_characterId) * self::RICH_REDUCTION);
            subtractBounty($p_characterId, $bountyReduction);
        }

        subtract_gold($p_characterId, floor(get_gold($p_characterId) * self::DOSHIN_CUT));
    }

    /**
     * Returns a view spec hash for rendering a template
     *
     * @param p_data Array Hash of variables to pass to the view
     * @return Array
     */
    private function render($p_data) {
        $myBounty = getBounty($this->sessionData['char_id']);

        // Pulling the bounties.
        $data = query_array("SELECT player_id, uname, bounty, class_name AS class, level, clan_id, clan_name 
            FROM players JOIN class ON class_id = _class_id LEFT JOIN clan_player ON player_id = _player_id 
            LEFT JOIN clan ON clan_id = _clan_id WHERE bounty > 0 AND active = 1 and health > 0 ORDER BY bounty DESC");

        $p_data['data'] = $data;
        $p_data['myBounty'] = $myBounty;

        return [
            'template' => 'doshin.tpl',
            'title'    => 'Doshin Office',
            'parts'    => $p_data,
            'options'  => [
                'quickstat' => $p_data['quickstat'],
            ],
        ];
    }
}
